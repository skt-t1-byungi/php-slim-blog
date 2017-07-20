import Comments from "./component/Comments";
import WriteComment from "./component/WriteComment";
import Login from "./component/Login";
import WritePost from "./component/WritePost";
import FileUploadBuilder from "./component/FileUplaod/Builder";

export default {
    login(){
        return new Login('#login');
    },

    comments(container) {
        return new Comments('#comments', container.get('login'));
    },

    writeComment(container) {
        return new WriteComment('#write-comment', container.get('login'));
    },

    writePost() {
        return new WritePost('#post-editor');
    },

    fileUploadBuilder(){
        return new FileUploadBuilder();
    }
};