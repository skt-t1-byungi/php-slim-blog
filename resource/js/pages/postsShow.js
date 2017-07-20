import env from "../env";
import container from "../container";

export default () => {

    /** @var Comments **/
    const comments = container.get('comments');

    /** @var WriteComment **/
    const writeComment = container.get('writeComment');

    if (env.isLogged) {
        comments
            .attachActionEvents()
            .autoHeightForms()
            .openFormIfMatchesFormId(location.hash);

        writeComment
            .autoHeight()
            .preventDoubleSubmit();

    } else {
        comments.openLoginOnActionClick();

        writeComment
            .disabled()
            .preventSubmit()
            .showLoginOnClick();
    }

    if (env.errors.comment) {
        writeComment.removeErrorBorderAfterFocus();
    }
}
