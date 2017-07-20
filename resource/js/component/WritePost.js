import $ from "jquery";
import {debounce} from "lodash";
import autosize from "autosize";

export default class WritePost {

    constructor(selector) {
        this.$el = $(selector);
        this.$title = this.$el.find('input[name=title]');
        this.$markdown = this.$el.find('textarea[name=markdown_content]');
        this.$parsed = this.$el.find('.markdown');

        this.parseHandler = null;
    }

    autoHeight() {
        autosize(this.$markdown);
        return this;
    }

    removeErrorBorderAfterFocus() {
        this.$title.one('focus', () => {
            this.$title.removeClass('form__text--error');
        });

        this.$markdown.one('focus', () => {
            this.$markdown.removeClass('form__textarea--error');
        });

        return this;
    }

    parseWhenMarkdownUpdate(parserUrl) {
        this.parseHandler = this.makeParseHandler(parserUrl);

        this.$markdown.on('input', debounce(this.parseHandler, 250));

        // 이벤트 바인딩 전, 입려된 value 가 존재할 수도 있습니다.
        // 최초 1회는 이벤트를 강제로 트리거합니다.
        this.parseHandler();

        return this;
    }

    makeParseHandler(parserUrl) {
        return () => {
            const markdown = this.$markdown.val();

            $.post(parserUrl, {markdown}).done((parsed) => {
                this.$parsed.html(parsed);
            });
        };
    }
    
    insert(string) {
        this.$markdown.append(string);

        this.parseHandler();
    }
}