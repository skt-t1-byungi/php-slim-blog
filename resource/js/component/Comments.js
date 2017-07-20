import $ from "jquery";
import autosize from "autosize";

export default class Comments {

    /**
     * @param {string} selector
     * @param {Login} login
     */
    constructor(selector, login) {
        this.$el = $(selector);
        this.login = login;

        this.$comments = this.$el.find('.comment');
        this.$commentInners = this.$comments.children('.comment__inner');
        this.$replyForms = this.$el.find('[id^=reply-comment-]');
        this.$editForms = this.$el.find('[id^=edit-comment-]');
        this.$allForms = this.$replyForms.add(this.$editForms);
    }

    openLoginOnActionClick() {
        this.$el.on('click', '[data-action]', () => {
            this.login.show();
            return false;
        });

        return this;
    }

    attachActionEvents() {
        this.$el.on('click', '[data-action]', ({target}) => {
            const $target = $(target);
            const commentId = $target.data('id');
            const action = $target.data('action');

            switch (action) {
                case 'delete':
                    this.onDeleteAction(commentId);
                    break;
                case 'edit':
                    this.onEditAction(commentId);
                    break;
                case 'reply':
                    this.onReplyAction(commentId);
                    break;
            }

            return false;
        });

        return this;
    }

    onDeleteAction(commentId) {
        if (confirm("정말 삭제하겠습니까?")) {
            location.href = `/comments/${commentId}/delete`;
        }
    }

    onEditAction(commentId) {
        const $editForm = this.getEditForm$(commentId);
        const $commentInner = this.getCommentInner$(commentId);

        this.$allForms.not($editForm).hide();
        this.$commentInners.not($commentInner).show();

        $commentInner.toggle();
        $editForm.toggle()
            .find('textarea').focus();

        autosize.update($editForm.get(0));
    }

    onReplyAction(commentId) {
        const $replyForm = this.getReplyForm$(commentId);

        this.$commentInners.show();
        this.$allForms.not($replyForm).hide();

        $replyForm.toggle()
            .find('textarea').focus();
    }

    getComment$(commentId) {
        return this.$comments.filter(`#comment-${commentId}`);
    }

    getCommentInner$(commentId) {
        return this.getComment$(commentId).children('.comment__inner');
    }

    getReplyForm$(commentId) {
        return this.$replyForms.filter(`#reply-comment-${commentId}`);
    }

    getEditForm$(commentId) {
        return this.$editForms.filter(`#edit-comment-${commentId}`);
    }

    autoHeightForms() {
        autosize(this.$allForms.find('textarea'));
        
        return this;
    }

    openFormIfMatchesFormId(hash) {
        const matches = hash.match(/^#(\w+?)-comment-(\d+)$/);

        if (!matches) {
            return this;
        }

        const [, action, commentId] = matches;

        switch (action) {
            case 'reply':
                this.onReplyAction(commentId);
                break;
            case 'edit':
                this.onEditAction(commentId);
                break;
        }

        return this;
    }

}