import "prismjs";
import "prismjs/components/prism-php";
import "prismjs/components/prism-php-extras";
import "prismjs/components/prism-typescript";

import page from "page";
import postsShow from "./pages/postsShow";
import postsWrite from "./pages/postsWrite";
import postsEdit from "./pages/postsEdit";
import "./pages/common";

//라우트
page('/', postsShow);
page('/posts/write', postsWrite);
page('/posts/:id/edit', postsEdit);
page('/posts/:id', postsShow);

page.start({click: false, popstate: false});