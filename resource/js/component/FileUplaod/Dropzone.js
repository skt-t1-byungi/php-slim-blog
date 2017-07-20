import DropzoneJs from "dropzone";

DropzoneJs.autoDiscover = false;

export default class Dropzone extends DropzoneJs {

    onSuccessFiles(listener) {
        this.on('success', (file, responseText) => {
            const files = JSON.parse(responseText);

            listener(files);
        });

        return this;
    }
}