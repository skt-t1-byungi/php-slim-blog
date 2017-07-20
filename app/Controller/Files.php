<?php

namespace App\Controller;

use App\Model\File;
use Illuminate\Support\Collection;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\Request;
use Slim\Http\Stream;

class Files
{
    const SAVE_PATH = PUBLIC_DIR . '/data';

    /**
     * json 스키마
     * {
     *      count:int
     *      totalBytes:int
     *      files:object{
     *          [file.id]{
     *              id:string
     *              isAdmin:bool
     *              path:string
     *              name:string
     *              bytes:int
     *              createdAt:int
     *          }
     *      }
     *  }
     *
     * @param string|null $postId
     * @return string
     */
    public function show($postId = null)
    {
        if (!auth()->isAdmin()) {
            abort(400);
        }

        return $this->jsonByFiles(File::where('post_id', $postId)->get());
    }

    public function upload(Request $request, $postId = null)
    {
        if (!auth()->isAdmin()) {
            abort(400);
        }

        $files = collect();

        /** @var UploadedFileInterface $file */
        foreach ($request->getUploadedFiles() as $file) {
            $path = $this->makeUniquePath();

            $files[] = File::create([
                'post_id' => $postId,
                'path'    => $path,
                'name'    => $file->getClientFilename(),
                'type'    => $file->getClientMediaType(),
                'bytes'   => $file->getSize()
            ]);

            $file->moveTo($moveTo = PUBLIC_DIR . $path);

            @chmod($moveTo, 0644);
        }

        return $this->jsonByFiles($files);
    }

    public function download($hash)
    {
        list($fileId) = hash_decode($hash);

        /** @var File $file */
        $file = File::findOrFail($fileId);

        $stream = new Stream(fopen(PUBLIC_DIR . $file->path, 'rb'));

        return response()
            ->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $file->name . '"')
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->withHeader('Pragma', 'public')
            ->withBody($stream);
    }

    public function delete(Request $request)
    {
        if (!auth()->isAdmin()) {
            abort(400);
        }

        /** @var File $file */
        $file = File::find($request->getParam('fileId'));

        @unlink(PUBLIC_DIR . $file->path);

        $file->delete();
    }

    /**
     * @return string
     */
    private function makeUniquePath()
    {
        return substr(tempnam(self::SAVE_PATH, uniqid()), strlen(PUBLIC_DIR));
    }

    private function jsonByFiles(Collection $files)
    {
        $parsed = (object)$files
            ->mapWithKeys(function (File $file) {
                $value = [
                    'id'        => $file->id,
                    'hash'      => hash_encode($file->id),
                    'path'      => $file->path,
                    'isImage'   => $file->isImage(),
                    'name'      => $file->name,
                    'bytes'     => $file->bytes,
                    'createdAt' => $file->created_at ? $file->created_at->timestamp : time()
                ];

                return [$file->id => $value];
            })
            ->toArray();

        return json_encode($parsed);
    }
}