<div style="height:100%;width:100%">
	<div style="position:absolute;top:50%;left:50%;background:#CCC;padding:16px;transform:translate3d(-50%,-50%,0)">
		<table style="width:100%;background:#F90;border:4px solid #F90;border-spacing:0">
			<tr>
				<td style="padding:16px">
					<a href="https://www.tastewiki.xyz"><img src="https://www.tastewiki.xyz/img/title.png" style="width:128px"/></a>
				</td>
			</tr>
			<tr style="background:#FFF">
				<td style="padding:16px">
					<p style="margin:0">
					<strong>{{ $data->nickname }}</strong> 님의 이메일 인증 확인 메일입니다.<br/>
					인증하려면 아래 버튼을 클릭해 주시기 바랍니다.
					</p>
				</td>
			</tr>
			<tr style="background:#FFF">
				<td style="padding:16px">
					<a href="https://www.tastewiki.xyz/Mail/checkVerify?mail={{ $data->email }}&code={{ $data->code }}" style="text-decoration:none">
						<div style="float:left;background:#F90;height:32px;width:128px;line-height:32px;color:#FFF;text-align:center;font-weight:bold;">이메일 인증하기</div>
					</a>
				</td>
			</tr>
		</table>
	</div>
</div>
