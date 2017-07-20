import $ from "jquery";
import {assign} from "lodash";

export default class Storage {

    constructor(fetchUrl, deleteUrl) {
        this.fetchUrl = fetchUrl;
        this.deleteUrl = deleteUrl;

        this.handlers = [];

        /**
         * @typedef {object} File
         * @property {int} id
         * @property {string} name
         * @property {int} bytes
         * @property {string} path
         * @property {bool} isImage
         * @property {int} createdAt
         *
         * @type {File[]}
         */
        this.files = {};
    }

    listenAddFilesByDropzone(dropzone) {
        dropzone.onSuccessFiles(files => this.addFiles(files));

        return this;
    }

    /**
     * @param fileId
     * @return {File}
     */
    getFile(fileId) {
        return this.files[fileId];
    }

    addFiles(files) {
        this.changeFiles(() => {
            assign(this.files, files);
        });
    }

    replaceFiles(files) {
        this.changeFiles(() => {
            this.files = files
        });
    }

    deleteFile(fileId) {
        this.changeFiles(() => {
            this.deleteServerFile(fileId);

            delete this.files[fileId];
        })
    }

    changeFiles(handle) {
        handle();

        this.triggerChangeEvent();
    }

    onChange(handler) {
        this.handlers.push(handler);
    }

    triggerChangeEvent() {
        for (let handler of this.handlers) {
            handler(this.files);
        }
    }

    fetchFilesFromServer() {
        $.getJSON(this.fetchUrl).done(files => this.replaceFiles(files));

        return this;
    }

    deleteServerFile(fileId) {
        return $.post(this.deleteUrl, {fileId});
    }
}