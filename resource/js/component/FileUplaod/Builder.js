import Dropzone from "./Dropzone";
import Table from "./Table";
import Storage from "./Storage";

export default class Builder {

    fileSyncUrls({fetchUrl, deleteUrl}) {
        this.fetchUrl = fetchUrl;
        this.deleteUrl = deleteUrl;

        return this;
    }

    forUpload(uploadUrl, csrfToken) {
        this.uploadUrl = uploadUrl;
        this.csrfToken = csrfToken;

        return this;
    }

    forTableView(selector, writePost, templateText) {
        this.tableSelector = selector;
        this.writePost = writePost;
        this.templateText = templateText;

        return this;
    }

    build() {
        const storage = new Storage(this.fetchUrl, this.deleteUrl);

        const dropzone = new Dropzone('#dropzone', {
            url: this.uploadUrl,
            headers: {
                'X-CSRF-TOKEN': this.csrfToken
            }
        });

        const tableView = new Table(this.tableSelector, storage, this.writePost, this.templateText);

        storage
            .listenAddFilesByDropzone(dropzone)
            .fetchFilesFromServer();

        tableView
            .listenChangeStorage()
            .attachEvents();
    }
}