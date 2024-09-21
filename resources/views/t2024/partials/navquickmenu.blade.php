<!-- BEGIN Quick Menu -->
<!-- to add more items, please make sure to change the variable '$menu-items: number;' in your _page-components-shortcut.scss -->
<nav class="shortcut-menu d-none d-sm-block">
	<input type="checkbox" class="menu-open" name="menu-open" id="menu_open" />
	<label for="menu_open" class="menu-open-button ">
		<span class="app-shortcut-icon d-block"></span>
	</label>
	<a href="#" class="menu-item btn" data-toggle="tooltip" data-placement="left" title="kembali ke atas">
		<i class="fal fa-arrow-up"></i>
	</a>
	<a onclick="event.preventDefault(); document.getElementById('logoutform').submit();" href="{{ trans('global.logout') }}" class="menu-item btn" data-toggle="tooltip" data-placement="left" title="Keluar">
		<i class="fal fa-sign-out"></i>
	</a>
	<a href="#" class="menu-item btn" data-action="app-fullscreen" data-toggle="tooltip" data-placement="left" title="layar penuh">
		<i class="fal fa-expand"></i>
	</a>
	<a href="#" class="menu-item btn" data-action="app-print" data-toggle="tooltip" data-placement="left" title="Cetak halaman">
		<i class="fal fa-print"></i>
	</a>
	<a hidden href="#" class="menu-item btn" data-action="app-voice" data-toggle="tooltip" data-placement="left" title="Voice command">
		<i class="fal fa-microphone"></i>
	</a>
</nav>
<!-- END Quick Menu -->
