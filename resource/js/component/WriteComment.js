import $ from "jquery";
import autosize from "autosize";

export default class WriteComment {

    /**
     * @param {string} selector
     * @param {Login} login
     */
    constructor(selector, login) {
        this.$el = $(selector);
        this.login = login;

        this.$textarea = this.$el.find('textarea');
    }

    disabled() {
        this.$textarea.prop('disabled', true);
        return this;
    }

    showLoginOnClick() {
        this.$el.click(() => this.login.show());
        return this;
    }

    preventSubmit() {
        this.$el.submit((event) => event.preventDefault());
        return this;
    }

    autoHeight() {
        autosize(this.$textarea);
        return this;
    }

    preventDoubleSubmit() {
        this.$el.one('submit', () => this.preventSubmit());
        return this;
    }

    removeErrorBorderAfterFocus() {
        this.$textarea.one('focus', () => {
            this.$el.find('.comment-editor__box').removeClass('comment-editor__box--error');
        });

        return this;
    }
}