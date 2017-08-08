import $ from "jquery";
import env from "../env";
import container from "../container";

// 모든 ajax 에 csrf 토큰 정보 추가합니다.
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': env.csrfToken
    }
});

// 로그인창 이벤트를 바인딩 합니다.
/** @var Login **/
const login = container.get('login');
login.attachEvent();

// document 에서 [data-login] 속성을 클릭하면 로그인창을 엽니다.
if (!env.isLogged) {
    $(document).on('click', '[data-login]', () => {
        login.show();
        return false;
    });
}

// 작은 사이즈 화면에서 감춰진 헤더가 트리거 버튼을 클릭하면 나옵니다. (반응형 처리)
const $header = $(".header");
$("[data-header-trigger]").click(() => {
    $header.toggleClass('header--triggered');
    return false;
});

//헤더 바탕 클릭시, 감추기 (반응형 처리)
$header.click(({target}) => {
    if (target === $header.get(0)) {
        $header.removeClass('header--triggered');
    }
});

$(document).on('click', '[data-confirm]', ({target}) => {
    if (!confirm(target.dataset.confirm)) {
        return false;
    }
});