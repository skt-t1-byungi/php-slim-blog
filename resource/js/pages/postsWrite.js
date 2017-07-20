import container from "../container";
import env from "../env";
import TextInputToArray from "../component/TextInputToArray";
import $ from "jquery";

export default () => {
    (new TextInputToArray('#tags-to-array', 'tags')).attachEvent();

    /** @var WritePost **/
    const writePost = container.get('writePost');

    /** @var Builder **/
    const fileUploadBuilder = container.get('fileUploadBuilder');

    fileUploadBuilder
        .fileSyncUrls({
            fetchUrl: env.routes.filesShow,
            deleteUrl: env.routes.fileDelete
        })
        .forUpload(env.routes.filesUpload, env.csrfToken)
        .forTableView("#file-table", writePost, $("#file-template").html())
        .build();

    writePost
        .autoHeight()
        .parseWhenMarkdownUpdate(env.routes.markdownParser);

    if (env.errors) {
        writePost.removeErrorBorderAfterFocus();
    }
}