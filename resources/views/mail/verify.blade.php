<div style="height:100%;width:100%">
	<div style="position:absolute;top:50%;left:50%;background:#CCCCCC;padding:16px;transform:translate3d(-50%,-50%,0)">
		<table style="width:100%;background:#FF9900">
			<tr>
				<td style="padding:16px">
					<a href="https://www.tastewiki.xyz"><img src="https://www.tastewiki.xyz/img/title.png" style="width:128px"/></a>
				</td>
			</tr>
			<tr style="background:#FFFFFF">
				<td style="padding:16px">
					<p style="margin:0">
					<strong>{{ $data->nickname }}</strong> 님의 이메일 인증코드 확인 메일입니다.<br/>
					인증코드는 <span style="color:#FF9900"><strong>[{{ $data->code }}]</strong></span> 입니다.
					</p>
				</td>
			</tr>
		</table>
	</div>
</div>