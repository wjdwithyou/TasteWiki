<div class="ta_c">
@if ($logined)
    <a href="{{ $adr_ctr }}Mypage/index"><div class="dropdown_item">마이페이지</div></a>
    <a onclick="logout();"><div class="dropdown_item">로그아웃</div></a>
@else
    <a href="{{ $adr_ctr }}Account/loginIndex"><div class="dropdown_item">로그인</div></a>
@endif
</div>
