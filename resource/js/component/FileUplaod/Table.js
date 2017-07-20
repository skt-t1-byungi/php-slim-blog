import $ from "jquery";
import {escape, filter, keys, size, template} from "lodash";
import {prettyBytes} from "../../helpers";

function makeImageTag(file) {
    return escape(`<img src="${file.path}">\n`);
}

function makeDownloadTag(file) {
    return escape(`[${file.name}(${prettyBytes(file.bytes)})](/files/download/${file.hash})`);
}

export default class Table {

    /**
     * @param {string} selector
     * @param {Storage} storage
     * @param {WritePost} writePost
     * @param {string} templateText
     */
    constructor(selector, storage, writePost, templateText) {
        this.$el = $(selector);
        this.storage = storage;
        this.writePost = writePost;
        this.compiled = template(templateText);

        this.$tbody = this.$el.find('tbody');
        this.$files = {};
        this.$emptyRow = this.$tbody.find('tr.empty');
    }

    listenChangeStorage() {
        this.storage.onChange(this.makeStorageListener());

        return this;
    }
    
    makeStorageListener() {
        return (files) => {
            //사라진 파일 행을 제거합니다.
            keys(this.$files)
                .filter(id => !files.hasOwnProperty(id))
                .forEach(id => this.removeFileRowById(id));

            //새 파일행을 추가합니다.
            filter(files, (file, id) => !this.hasFileRowById(id))
                .forEach(file => this.addFileRow(file));

            this.$emptyRow.toggle(this.getRowCount() === 0);
        };
    }

    attachEvents() {
        this
            .attachBubbleClickEvent('delete', (fileId) => {
                this.storage.deleteFile(fileId);
            })
            .attachBubbleClickEvent('image', (fileId, file) => {
                this.writePost.insert(makeImageTag(file));
            })
            .attachBubbleClickEvent('download', (fileId, file) => {
                this.writePost.insert(makeDownloadTag(file));
            });

        return this;
    }

    attachBubbleClickEvent(name, handle) {
        this.$el.on('click', `[data-${name}]`, ({target}) => {
            const fileId = $(target).data(name);
            const file = this.storage.getFile(fileId);

            handle(fileId, file);

            return false;
        });

        return this;
    }

    hasFileRowById(id) {
        return this.$files.hasOwnProperty(id);
    }

    addFileRow(file) {
        const html = this.compiled({
            id: file.id,
            size: prettyBytes(file.bytes),
            name: file.name,
            isImage: file.isImage,
            createdAt: (new Date(file.createdAt * 1000)).toLocaleString()
        });

        const $file = this.$files[file.id] = $(html);

        this.$tbody.append($file);
    }

    removeFileRowById(id) {
        this.$files[id].remove();
        delete this.$files[id];
    }

    getRowCount() {
        return size(this.$files);
    }
}