import $ from "jquery";
import {debounce, keys} from "lodash";

export default class TextInputToArray {
    constructor(selector, name, delimiter = ',') {
        this.$textInput = $(selector);
        this.name = name;
        this.delimiter = delimiter;
        this.toArray$ = {};
    }

    attachEvent() {
        const eventHandler = this.makeEventHandler();

        this.$textInput.on('input', debounce(eventHandler, 250));

        // 이벤트 바인딩 전, 입려된 value 가 존재할 수도 있습니다.
        // 최초 1회는 이벤트를 강제로 트리거합니다.
        eventHandler();
    }

    makeEventHandler() {
        return () => {
            const words = this.$textInput.val()
                .split(this.delimiter)
                .map(word => word.trim())
                .filter(word => !!word);

            keys(this.toArray$)
                .filter(word => !words.includes(word))
                .forEach((word) => this.remove(word));

            words
                .filter(word => !this.has(word))
                .forEach(word => this.add(word));
        };
    }

    has(word) {
        return this.toArray$.hasOwnProperty(word);
    }

    add(word) {
        const $item = $(`<input type="hidden" name="${this.name}[]">`).val(word);
        this.toArray$[word] = $item;
        this.$textInput.after($item);
    }

    remove(word) {
        this.toArray$[word].remove();
        delete this.toArray$[word];
    }
}