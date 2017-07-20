import $ from "jquery";

export default class Login {
    constructor(selector) {
        this.$el = $(selector);
    }

    attachEvent() {
        this.$el.click(({target}) => {
            if (target === this.$el.get(0)) {
                this.hide();
            }
        });
    }

    show() {
        this.$el.addClass('active');
    }

    hide() {
        this.$el.removeClass('active');
    }
}