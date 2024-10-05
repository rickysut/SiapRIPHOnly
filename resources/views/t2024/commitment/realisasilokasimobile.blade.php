@extends('t2024.layouts.admin')
@section('content')
{{-- @include('t2024.partials.breadcrumb') --}}
@include('t2024.partials.subheader')
@include('t2024.partials.sysalert')
{{-- @can('spatial_data_access') --}}
	<div class="row">
		<div id="myMap" style="height:500px; width: 100%;"></div>
		<div class="panel">
			<div class="panel-container">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<div class="panel" id="panel-peta">
				<div class="panel-container show">
					<div class="accordion" id="data-laporan">
						<div class="card">
							<div class="card-header">
								<div class="card-title" data-toggle="collapse" data-target="#data-realisasi" aria-expanded="true">
									<div class="d-flex flex-row align-items-center">
										<div class="info-card-text">
											<div class="text-truncate text-truncate-lg">Data Realisasi: {{$data['lokasi']->kode_spatial}}</div>
										</div>
									</div>
									<span class="ml-auto align-self-start">
										<span class="collapsed-reveal">
											<i class="fal fa-chevron-up fs-xl"></i>
										</span>
										<span class="collapsed-hidden">
											<i class="fal fa-chevron-down fs-xl"></i>
										</span>
									</span>
								</div>
							</div>
							<div id="data-realisasi" class="collapse show" data-parent="#data-laporan" style="">
								<div class="card-body">
									<ul class="list-group mb-3">
										<li class="list-group-item d-flex justify-content-between align-item-start">
											<span class="text-muted">Nama Petani</span>
											<span class="fw-bold" id="">{{$data['spatial']->nama_petani}}</span>
										</li>
										<li class="list-group-item d-flex justify-content-between align-item-start">
											<span class="text-muted">NIK Petani</span>
											<span class="fw-bold" id="">{{$data['spatial']->ktp_petani}}</span>
										</li>
										<li class="list-group-item d-flex justify-content-between align-item-start">
											<span class="text-muted">Luas Lahan (m2)</span>
											<span class="fw-bold" id="">{{ number_format($data['spatial']->luas_lahan, 0, ',', '.') }}</span>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel" id="panel-2">
				<div class="panel-hdr">
					<h2>Log Kegiatan</h2>
					<div class="panel-toolbar">
						@include('t2024.partials.globaltoolbar')
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<div class="accordion accordion-hover accordion-outline" id="logKeg">
							<div class="card">
								<div class="card-header">
									<a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#logKeg-lahan" aria-expanded="false">
										<i class="fal fa-shovel width-2 fs-xl"></i>
										Pengolahan Lahan
										<span class="ml-auto">
											<span class="collapsed-reveal">
												<i class="fal fa-chevron-up fs-xl"></i>
											</span>
											<span class="collapsed-hidden">
												<i class="fal fa-chevron-down fs-xl"></i>
											</span>
										</span>
									</a>
								</div>
								<div id="logKeg-lahan" class="collapse" data-parent="#logKeg" style="">
									<form action="{{ route('2024.user.commitment.storefoto', ['noIjin' => $ijin, 'spatial' => $data['spatial']->kode_spatial]) }}" enctype="multipart/form-data" method="post">
										@csrf
										<div class="card-body">
											<div class="row d-flex">
												<div class="col-md-4 mb-3">
													<div class="card" style="width: 100%; padding-top: 100%; position: relative; overflow: hidden;">
														<div class="card-image" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;
															background-image: url('{{ $data['lokasi']->lahanfoto ? asset("storage/{$data['lokasi']->lahanfoto}") : asset("img/posts_img/default-post-image-light.svg") }}');
															background-size: cover; background-repeat: no-repeat; background-position: center;">
														</div>
													</div>
												</div>
												<div class="col-md-8">
													<div class="fom-group mb-3 d-md-none d-block">
														<button class="btn btn-block btn-primary" type="button" id="camlahan">
															<i class="fal fa-camera mr-1"></i> Foto Kegiatan
														</button>
													</div>
													<div class="form-group">
														<label class="form-label" for="lahandate">Tanggal</label>
														<input class="form-control" id="lahandate" type="date" name="lahandate" value="{{$data['lokasi']->lahandate}}">
													</div>
													<div class="form-group">
														<label class="form-label" for="lahancomment">Keterangan</label>
														<textarea class="form-control" id="lahancomment" name="lahancomment" rows="2">{{$data['lokasi']->lahancomment}}</textarea>
													</div>
													<div class="form-group "> {{-- d-none d-lg-block --}}
														<label class="form-label" for="lahanfoto">Bukti Kegiatan</label>
														<div class="input-group">
															<div class="custom-file">
																<input type="file" class="custom-file-input" id="lahanfoto" name="lahanfoto" aria-describedby="lahanfoto" accept=".jpeg, .jpg, .png" value="$data['lokasi']->lahanfoto" capture="camera">
																<label class="custom-file-label" for="lahanfoto">Cari foto</label>
															</div>
														</div>
														<label for="lahanfoto" class="help-block text-truncate text-truncate-lg">{{$data['lokasi']->lahanfoto }}</label>
													</div>
												</div>
											</div>
										</div>
										<div class="card-footer d-flex justify-content-between">
											<div></div>
											<div class="ml-auto">
												<button class="btn btn-warning waves-effect waves-themed btnSave" type="submit" id="saveLahan" data-field="lahanfoto">Simpan Persiapan Lahan</button>
											</div>
										</div>
									</form>
								</div>
							</div>
							<div class="card">
								<div class="card-header">
									<a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#logKeg-benih" aria-expanded="false">
										<i class="fal fa-seedling width-2 fs-xl"></i>
										Persiapan Benih
										<span class="ml-auto">
											<span class="collapsed-reveal">
												<i class="fal fa-chevron-up fs-xl"></i>
											</span>
											<span class="collapsed-hidden">
												<i class="fal fa-chevron-down fs-xl"></i>
											</span>
										</span>
									</a>
								</div>
								<div id="logKeg-benih" class="collapse" data-parent="#logKeg" style="">
									<form action="{{ route('2024.user.commitment.storefoto', ['noIjin' => $ijin, 'spatial' => $data['spatial']->kode_spatial]) }}" enctype="multipart/form-data" method="post">
										@csrf
										<div class="card-body">
											<div class="row d-flex">
												<div class="col-md-4 mb-3">
													<div class="card" style="width: 100%; padding-top: 100%; position: relative; overflow: hidden;">
														<div class="card-image" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;
															background-image: url('{{ $data['lokasi']->benihFoto ? asset("storage/{$data['lokasi']->benihFoto}") : asset("img/posts_img/default-post-image-light.svg") }}');
															background-size: cover; background-repeat: no-repeat; background-position: center;">
														</div>
													</div>
												</div>
												<div class="col-md-8">
													<div class="fom-group mb-3 d-md-none d-block">
														<button class="btn btn-block btn-primary"  id="camBenih">
															<i class="fal fa-camera mr-1"></i> Foto Kegiatan
														</button>
													</div>
													<div class="form-group">
														<label class="form-label" for="benihDate">Tanggal</label>
														<input class="form-control" id="benihDate" type="date" name="benihDate" value="{{$data['lokasi']->benihDate}}">
													</div>
													<div class="form-group">
														<label class="form-label" for="benihComment">Keterangan</label>
														<textarea class="form-control" id="benihComment" name="benihComment" rows="2">{{$data['lokasi']->benihComment}}</textarea>
													</div>
													<div class="form-group d-none d-lg-block">
														<label class="form-label" for="benihFoto">Bukti Kegiatan</label>
														<div class="input-group">
															<div class="custom-file">
																<input type="file" class="custom-file-input" id="benihFoto" name="benihFoto" aria-describedby="benihFoto" accept=".jpeg, .jpg, .png">
																<label class="custom-file-label" for="benihFoto">input foto kegiatan</label>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card-footer d-flex justify-content-between">
											<div></div>
											<div class="ml-auto">
												<button class="btn btn-warning waves-effect waves-themed btnSave" type="submit" id="saveBenih" data-field="benihFoto">Simpan Kegiatan Benih</button>
											</div>
										</div>
									</form>
								</div>
							</div>
							<div class="card">
								<div class="card-header">
									<a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#logKeg-mulsa" aria-expanded="false">
										<i class="fal fa-blanket width-2 fs-xl"></i>
										Pemasangan Mulsa
										<span class="ml-auto">
											<span class="collapsed-reveal">
												<i class="fal fa-chevron-up fs-xl"></i>
											</span>
											<span class="collapsed-hidden">
												<i class="fal fa-chevron-down fs-xl"></i>
											</span>
										</span>
									</a>
								</div>
								<div id="logKeg-mulsa" class="collapse" data-parent="#logKeg" style="">
									<form action="{{ route('2024.user.commitment.storefoto', ['noIjin' => $ijin, 'spatial' => $data['spatial']->kode_spatial]) }}" enctype="multipart/form-data" method="post">
										@csrf
										<div class="card-body">
											<div class="row d-flex">
												<div class="col-md-4 mb-3">
													<div class="card" style="width: 100%; padding-top: 100%; position: relative; overflow: hidden;">
														<div class="card-image" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;
															background-image: url('{{ $data['lokasi']->mulsaFoto ? asset("storage/{$data['lokasi']->mulsaFoto}") : asset("img/posts_img/default-post-image-light.svg") }}');
															background-size: cover; background-repeat: no-repeat; background-position: center;">
														</div>
													</div>
												</div>
												<div class="col-md-8">
													<div class="fom-group mb-3 d-md-none d-block">
														<button class="btn btn-block btn-primary" id="mulsaCam">
															<i class="fal fa-camera mr-1"></i> Foto Kegiatan
														</button>
													</div>
													<div class="form-group">
														<label class="form-label" for="mulsaDate">Tanggal</label>
														<input class="form-control" id="mulsaDate" type="date" name="mulsaDate" value="{{$data['lokasi']->mulsaDate}}">
													</div>
													<div class="form-group">
														<label class="form-label" for="mulsaComment">Keterangan</label>
														<textarea class="form-control" id="mulsaComment" name="mulsaComment" rows="2">{{$data['lokasi']->mulsaComment}}</textarea>
													</div>
													<div class="form-group d-none d-lg-block">
														<label class="form-label" for="mulsaFoto">Bukti Kegiatan</label>
														<div class="input-group">
															<div class="custom-file">
																<input type="file" class="custom-file-input" id="mulsaFoto" name="mulsaFoto" aria-describedby="mulsaFoto" accept=".jpeg, .jpg, .png">
																<label class="custom-file-label" for="mulsaFoto">input foto kegiatan</label>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card-footer d-flex justify-content-between">
											<div></div>
											<div class="ml-auto">
												<button class="btn btn-warning waves-effect waves-themed btnSave" type="submit" id="saveMulsa"  data-field="mulsaFoto">Simpan Kegiatan Mulsa</button>
											</div>
										</div>
									</form>
								</div>
							</div>
							<div class="card">
								<div class="card-header">
									<a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#logKeg-tanam" aria-expanded="false">
										<i class="fal fa-hand-holding-seedling width-2 fs-xl"></i>
										Penanaman
										<span class="ml-auto">
											<span class="collapsed-reveal">
												<i class="fal fa-chevron-up fs-xl"></i>
											</span>
											<span class="collapsed-hidden">
												<i class="fal fa-chevron-down fs-xl"></i>
											</span>
										</span>
									</a>
								</div>
								<div id="logKeg-tanam" class="collapse" data-parent="#logKeg" style="">
									<form action="{{ route('2024.user.commitment.storefoto', ['noIjin' => $ijin, 'spatial' => $data['spatial']->kode_spatial]) }}" enctype="multipart/form-data" method="post">
										@csrf
										<div class="card-body">
											<div class="row d-flex">
												<div class="col-md-4 mb-3">
													<div class="card" style="width: 100%; padding-top: 100%; position: relative; overflow: hidden;">
														<div class="card-image" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;
															background-image: url('{{ $data['lokasi']->tanamFoto ? asset("storage/{$data['lokasi']->tanamFoto}") : asset("img/posts_img/default-post-image-light.svg") }}');
															background-size: cover; background-repeat: no-repeat; background-position: center;">
														</div>
													</div>
												</div>
												<div class="col-md-8">
													<div class="fom-group mb-3 d-md-none d-block">
														<button class="btn btn-block btn-primary" id="tanamCam">
															<i class="fal fa-camera mr-1"></i> Foto Kegiatan
														</button>
													</div>
													<div class="form-group">
														<label class="form-label" for="tanamDate">Tanggal</label>
														<input class="form-control" id="tanamDate" type="date" name="tanamDate" value="{{$data['lokasi']->tgl_tanam}}">
													</div>
													<div class="form-group">
														<label class="form-label" for="tanamLuas">Luas Tanam (m)</label>
														<input class="form-control" id="tanamLuas" type="number" name="tanamLuas" value="{{$data['lokasi']->luas_tanam}}">
													</div>
													<div class="form-group">
														<label class="form-label" for="tanamComment">Keterangan</label>
														<textarea class="form-control" id="tanamComment" name="tanamComment" rows="2">{{$data['lokasi']->tanamComment}}</textarea>
													</div>
													<div class="form-group d-none d-lg-block">
														<label class="form-label" for="tanamFoto">Bukti Kegiatan</label>
														<div class="input-group">
															<div class="custom-file">
																<input type="file" class="custom-file-input" id="tanamFoto" name="tanamFoto" aria-describedby="tanamFoto" accept=".jpeg, .jpg, .png">
																<label class="custom-file-label" for="tanamFoto">input foto kegiatan</label>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card-footer d-flex justify-content-between">
											<div></div>
											<div class="ml-auto">
												<button class="btn btn-warning waves-effect waves-themed btnSave" type="submit" id="saveTanam">Simpan Kegiatan Tanam</button>
											</div>
										</div>
									</form>
								</div>
							</div>
							<div class="card">
								<div class="card-header">
									<a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#logKeg-pupuk-1" aria-expanded="false">
										<i class="fal fa-vial width-2 fs-xl"></i>
										Pemupukan Pertama
										<span class="ml-auto">
											<span class="collapsed-reveal">
												<i class="fal fa-chevron-up fs-xl"></i>
											</span>
											<span class="collapsed-hidden">
												<i class="fal fa-chevron-down fs-xl"></i>
											</span>
										</span>
									</a>
								</div>
								<div id="logKeg-pupuk-1" class="collapse" data-parent="#logKeg" style="">
									<form action="{{ route('2024.user.commitment.storefoto', ['noIjin' => $ijin, 'spatial' => $data['spatial']->kode_spatial]) }}" enctype="multipart/form-data" method="post">
										@csrf
										<div class="card-body">
											<div class="row d-flex">
												<div class="col-md-4 mb-3">
													<div class="card" style="width: 100%; padding-top: 100%; position: relative; overflow: hidden;">
														<div class="card-image" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;
															background-image: url('{{ $data['lokasi']->pupuk1Foto ? asset("storage/{$data['lokasi']->pupuk1Foto}") : asset("img/posts_img/default-post-image-light.svg") }}');
															background-size: cover; background-repeat: no-repeat; background-position: center;">
														</div>
													</div>
												</div>
												<div class="col-md-8">
													<div class="fom-group mb-3 d-md-none d-block">
														<button class="btn btn-block btn-primary" id="pupuk1Cam">
															<i class="fal fa-camera mr-1"></i> Foto Kegiatan
														</button>
													</div>
													<div class="form-group">
														<label class="form-label" for="pupuk1Date">Tanggal</label>
														<input class="form-control" id="pupuk1Date" type="date" name="pupuk1Date" value="{{$data['lokasi']->pupuk1Date}}">
													</div>
													<div class="form-group">
														<label class="form-label" for="pupuk1Comment">Keterangan</label>
														<textarea class="form-control" id="pupuk1Comment" name="pupuk1Comment" rows="2">{{$data['lokasi']->pupuk1Comment}}</textarea>
													</div>
													<div class="form-group d-none d-lg-block">
														<label class="form-label" for="pupuk1Foto">Bukti Kegiatan</label>
														<div class="input-group">
															<div class="custom-file">
																<input type="file" class="custom-file-input" id="pupuk1Foto" name="pupuk1Foto" aria-describedby="pupuk1Foto" accept=".jpeg, .jpg, .png">
																<label class="custom-file-label" for="pupuk1Foto">input foto kegiatan</label>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card-footer d-flex justify-content-between">
											<div></div>
											<div class="ml-auto">
												<button class="btn btn-warning waves-effect waves-themed btnSave" type="submit" id="savePupuk1"  data-field="pupuk1Foto">Simpan Pemupukan 1</button>
											</div>
										</div>
									</form>
								</div>
							</div>
							<div class="card">
								<div class="card-header">
									<a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#logKeg-pupuk-2" aria-expanded="false">
										<i class="fal fa-vials width-2 fs-xl"></i>
										Pemupukan Kedua
										<span class="ml-auto">
											<span class="collapsed-reveal">
												<i class="fal fa-chevron-up fs-xl"></i>
											</span>
											<span class="collapsed-hidden">
												<i class="fal fa-chevron-down fs-xl"></i>
											</span>
										</span>
									</a>
								</div>
								<div id="logKeg-pupuk-2" class="collapse" data-parent="#logKeg" style="">
									<form action="{{ route('2024.user.commitment.storefoto', ['noIjin' => $ijin, 'spatial' => $data['spatial']->kode_spatial]) }}" enctype="multipart/form-data" method="post">
										@csrf
										<div class="card-body">
											<div class="row d-flex">
												<div class="col-md-4 mb-3">
													<div class="card" style="width: 100%; padding-top: 100%; position: relative; overflow: hidden;">
														<div class="card-image" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;
															background-image: url('{{ $data['lokasi']->pupuk2Foto ? asset("storage/{$data['lokasi']->pupuk2Foto}") : asset("img/posts_img/default-post-image-light.svg") }}');
															background-size: cover; background-repeat: no-repeat; background-position: center;">
														</div>
													</div>
												</div>
												<div class="col-md-8">
													<div class="fom-group mb-3 d-md-none d-block">
														<button class="btn btn-block btn-primary" id="pupuk2Cam">
															<i class="fal fa-camera mr-1"></i> Foto Kegiatan
														</button>
													</div>
													<div class="form-group">
														<label class="form-label" for="pupuk2Date">Tanggal</label>
														<input class="form-control" id="pupuk2Date" type="date" name="pupuk2Date" value="{{$data['lokasi']->pupuk2Date}}">
													</div>
													<div class="form-group">
														<label class="form-label" for="pupuk2Comment">Keterangan</label>
														<textarea class="form-control" id="pupuk2Comment" name="pupuk2Comment" rows="2">{{$data['lokasi']->pupuk2Comment}}</textarea>
													</div>
													<div class="form-group d-none d-lg-block">
														<label class="form-label" for="pupuk2Foto">Bukti Kegiatan</label>
														<div class="input-group">
															<div class="custom-file">
																<input type="file" class="custom-file-input" id="pupuk2Foto" name="pupuk2Foto" aria-describedby="pupuk2Foto" accept=".jpeg, .jpg, .png">
																<label class="custom-file-label" for="pupuk2Foto">input foto kegiatan</label>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card-footer d-flex justify-content-between">
											<div></div>
											<div class="ml-auto">
												<button class="btn btn-warning waves-effect waves-themed btnSave" type="submit" id="savePupuk2"  data-field="pupuk2Foto">Simpan Pemupukan 2</button>
											</div>
										</div>
									</form>
								</div>
							</div>
							<div class="card">
								<div class="card-header">
									<a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#logKeg-pupuk-3" aria-expanded="false">
										<i class="fal fa-fill-drip width-2 fs-xl"></i>
										Pemupukan Ketiga
										<span class="ml-auto">
											<span class="collapsed-reveal">
												<i class="fal fa-chevron-up fs-xl"></i>
											</span>
											<span class="collapsed-hidden">
												<i class="fal fa-chevron-down fs-xl"></i>
											</span>
										</span>
									</a>
								</div>
								<div id="logKeg-pupuk-3" class="collapse" data-parent="#logKeg" style="">
									<form action="{{ route('2024.user.commitment.storefoto', ['noIjin' => $ijin, 'spatial' => $data['spatial']->kode_spatial]) }}" enctype="multipart/form-data" method="post">
										@csrf
										<div class="card-body">
											<div class="row d-flex">
												<div class="col-md-4 mb-3">
													<div class="card" style="width: 100%; padding-top: 100%; position: relative; overflow: hidden;">
														<div class="card-image" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;
															background-image: url('{{ $data['lokasi']->pupuk3Foto ? asset("storage/{$data['lokasi']->pupuk3Foto}") : asset("img/posts_img/default-post-image-light.svg") }}');
															background-size: cover; background-repeat: no-repeat; background-position: center;">
														</div>
													</div>
												</div>
												<div class="col-md-8">
													<div class="fom-group mb-3 d-md-none d-block">
														<button class="btn btn-block btn-primary" id="pupuk3Cam">
															<i class="fal fa-camera mr-1"></i> Foto Kegiatan
														</button>
													</div>
													<div class="form-group">
														<label class="form-label" for="pupuk3Date">Tanggal</label>
														<input class="form-control" id="pupuk3Date" type="date" name="pupuk3Date" value="{{$data['lokasi']->pupuk3Date}}">
													</div>
													<div class="form-group">
														<label class="form-label" for="pupuk3Comment">Keterangan</label>
														<textarea class="form-control" id="pupuk3Comment" name="pupuk3Comment" rows="2">{{$data['lokasi']->pupuk3Comment}}</textarea>
													</div>
													<div class="form-group d-none d-lg-block">
														<label class="form-label" for="pupuk3Foto">Bukti Kegiatan</label>
														<div class="input-group">
															<div class="custom-file">
																<input type="file" class="custom-file-input" id="pupuk3Foto" name="pupuk3Foto" aria-describedby="pupuk3Foto" accept=".jpeg, .jpg, .png">
																<label class="custom-file-label" for="pupuk3Foto">input foto kegiatan</label>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card-footer d-flex justify-content-between">
											<div></div>
											<div class="ml-auto">
												<button class="btn btn-warning waves-effect waves-themed btnSave" type="submit" id="savePupuk3"  data-field="pupuk3Foto">Simpan Pemupukan 3</button>
											</div>
										</div>
									</form>
								</div>
							</div>
							<div class="card">
								<div class="card-header">
									<a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#logKeg-OPT" aria-expanded="false">
										<i class="fal fa-shield-virus width-2 fs-xl"></i>
										Pengendalian OPT
										<span class="ml-auto">
											<span class="collapsed-reveal">
												<i class="fal fa-chevron-up fs-xl"></i>
											</span>
											<span class="collapsed-hidden">
												<i class="fal fa-chevron-down fs-xl"></i>
											</span>
										</span>
									</a>
								</div>
								<div id="logKeg-OPT" class="collapse" data-parent="#logKeg" style="">
									<form action="{{ route('2024.user.commitment.storefoto', ['noIjin' => $ijin, 'spatial' => $data['spatial']->kode_spatial]) }}" enctype="multipart/form-data" method="post">
										@csrf
										<div class="card-body">
											<div class="row d-flex">
												<div class="col-md-4 mb-3">
													<div class="card" style="width: 100%; padding-top: 100%; position: relative; overflow: hidden;">
														<div class="card-image" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;
															background-image: url('{{ $data['lokasi']->optFoto ? asset("storage/{$data['lokasi']->optFoto}") : asset("img/posts_img/default-post-image-light.svg") }}');
															background-size: cover; background-repeat: no-repeat; background-position: center;">
														</div>
													</div>
												</div>
												<div class="col-md-8">
													<div class="fom-group mb-3 d-md-none d-block">
														<button class="btn btn-block btn-primary" id="optCam">
															<i class="fal fa-camera mr-1"></i> Foto Kegiatan
														</button>
													</div>
													<div class="form-group">
														<label class="form-label" for="optDate">Tanggal</label>
														<input class="form-control" id="optDate" type="date" name="optDate" value="{{$data['lokasi']->optDate}}">
													</div>
													<div class="form-group">
														<label class="form-label" for="optComment">Keterangan</label>
														<textarea class="form-control" id="optComment" name="optComment" rows="2">{{$data['lokasi']->optComment}}</textarea>
													</div>
													<div class="form-group d-none d-lg-block">
														<label class="form-label" for="optFoto">Bukti Kegiatan</label>
														<div class="input-group">
															<div class="custom-file">
																<input type="file" class="custom-file-input" id="optFoto" name="optFoto" aria-describedby="optFoto" accept=".jpeg, .jpg, .png">
																<label class="custom-file-label" for="optFoto">input foto kegiatan</label>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card-footer d-flex justify-content-between">
											<div></div>
											<div class="ml-auto">
												<button class="btn btn-warning waves-effect waves-themed btnSave" type="submit" id="saveOPT" data-field="optFoto">Simpan Pengendalian OPT</button>
											</div>
										</div>
									</form>
								</div>
							</div>
							<div class="card">
								<div class="card-header">
									<a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#logKeg-produksi" aria-expanded="false">
										<i class="fal fa-dolly width-2 fs-xl"></i>
										Produksi/Panen
										<span class="ml-auto">
											<span class="collapsed-reveal">
												<i class="fal fa-chevron-up fs-xl"></i>
											</span>
											<span class="collapsed-hidden">
												<i class="fal fa-chevron-down fs-xl"></i>
											</span>
										</span>
									</a>
								</div>
								<div id="logKeg-produksi" class="collapse" data-parent="#logKeg" style="">
									<form action="{{ route('2024.user.commitment.storefoto', ['noIjin' => $ijin, 'spatial' => $data['spatial']->kode_spatial]) }}" enctype="multipart/form-data" method="post">
										@csrf
										<div class="card-body">
											<div class="row d-flex">
												<div class="col-md-4 mb-3">
													<div class="card" style="width: 100%; padding-top: 100%; position: relative; overflow: hidden;">
														<div class="card-image" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;
															background-image: url('{{ $data['lokasi']->prodFoto ? asset("storage/{$data['lokasi']->prodFoto}") : asset("img/posts_img/default-post-image-light.svg") }}');
															background-size: cover; background-repeat: no-repeat; background-position: center;">
														</div>
													</div>
												</div>
												<div class="col-md-8">
													<div class="fom-group mb-3 d-md-none d-block">
														<button class="btn btn-block btn-primary" id="prodCam">
															<i class="fal fa-camera mr-1"></i> Foto Kegiatan
														</button>
													</div>
													<div class="form-group">
														<label class="form-label" for="prodDate">Tanggal</label>
														<input class="form-control" id="prodDate" type="date" name="prodDate" value="{{$data['lokasi']->tgl_panen}}">
													</div>
													<div class="form-group">
														<label class="form-label" for="prodVol">Volume Panen</label>
														<input class="form-control" id="prodVol" type="number" name="prodVol" value="{{$data['lokasi']->volume}}">
													</div>
													<div class="form-group">
														<label class="form-label" for="prodComment">Keterangan</label>
														<textarea class="form-control" id="prodComment" name="prodComment" rows="2">{{$data['lokasi']->prodComment}}</textarea>
													</div>
													<div class="form-group d-none d-lg-block">
														<label class="form-label" for="prodFoto">Bukti Kegiatan</label>
														<div class="input-group">
															<div class="custom-file">
																<input type="file" class="custom-file-input" id="prodFoto" name="prodFoto" aria-describedby="prodFoto" accept=".jpeg, .jpg, .png">
																<label class="custom-file-label" for="prodFoto">Cari foto</label>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card-footer d-flex justify-content-between">
											<div></div>
											<div class="ml-auto">
												<button class="btn btn-warning waves-effect waves-themed btnSave" type="submit" id="saveProd" data-field="prodFoto">Simpan Data Produksi</button>
											</div>
										</div>
									</form>
								</div>
							</div>
							<div class="card">
								<div class="card-header">
									<a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#logKeg-distribusi" aria-expanded="false">
										<i class="fal fa-truck-container width-2 fs-xl"></i>
										Distribusi Hasil
										<span class="ml-auto">
											<span class="collapsed-reveal">
												<i class="fal fa-chevron-up fs-xl"></i>
											</span>
											<span class="collapsed-hidden">
												<i class="fal fa-chevron-down fs-xl"></i>
											</span>
										</span>
									</a>
								</div>
								<div id="logKeg-distribusi" class="collapse" data-parent="#logKeg" style="">
									<form action="{{ route('2024.user.commitment.storefoto', ['noIjin' => $ijin, 'spatial' => $data['spatial']->kode_spatial]) }}" enctype="multipart/form-data" method="post">
										@csrf
										<div class="card-body">
											<div class="row d-flex">
												<div class="col-md-4 mb-3">
													<div class="card" style="width: 100%; padding-top: 100%; position: relative; overflow: hidden;">
														<div class="card-image" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;
															background-image: url('{{ $data['lokasi']->distFoto ? asset("storage/{$data['lokasi']->distFoto}") : asset("img/posts_img/default-post-image-light.svg") }}');
															background-size: cover; background-repeat: no-repeat; background-position: center;">
														</div>
													</div>
												</div>
												<div class="col-md-8">
													<div class="fom-group mb-3 d-md-none d-block">
														<button class="btn btn-block btn-primary" id="distCam">
															<i class="fal fa-camera mr-1"></i> Foto/Bukti Penjualan
														</button>
													</div>
													<div class="form-group">
														<label class="form-label" for="distStored">Untuk Benih</label>
														<input class="form-control" id="distStored" type="number" name="distStored" value="{{$data['lokasi']->vol_benih}}">
													</div>
													<div class="form-group">
														<label class="form-label" for="distSale">Untuk Dijual</label>
														<input class="form-control" id="distSale" type="number" name="distSale" value="{{$data['lokasi']->vol_jual}}">
													</div>
													<div class="form-group">
														<label class="form-label" for="distComment">Keterangan</label>
														<textarea class="form-control" id="distComment" name="distComment" rows="2">{{$data['lokasi']->distComment}}</textarea>
													</div>
													<div class="form-group d-none d-lg-block">
														<label class="form-label" for="distFoto">Bukti Kegiatan</label>
														<div class="input-group">
															<div class="custom-file">
																<input type="file" class="custom-file-input" id="distFoto" name="distFoto" aria-describedby="distFoto" accept=".jpeg, .jpg, .png">
																<label class="custom-file-label" for="distFoto">Cari foto</label>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card-footer d-flex justify-content-between">
											<div></div>
											<div class="ml-auto">
												<button class="btn btn-warning waves-effect waves-themed btnSave" type="submit" id="saveDist" data-field="distFoto">Simpan Distribusi Produksi</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

{{-- @endcans --}}

@endsection

<!-- start script for this page -->
@section('scripts')
@parent
<script>
	$(document).ready(function() {
		$('#vol_benih').prop('disabled', true);
    	// $('#vol_jual').prop('readonly', true);

		function updateVolJual() {
			var volume = parseFloat($('#volume').val());
			var volBenih = parseFloat($('#vol_benih').val()) || 0; // Treat null as 0

			if (!isNaN(volume) && volume >= 0 && volBenih >= 0 && volBenih <= volume) {
				$('#vol_jual').val(volume - volBenih);
			} else {
				$('#vol_jual').val('');
			}
		}

		// Reset vol_benih and vol_jual on change of volume
		$('#volume').on('input', function() {
			var volume = parseFloat($(this).val());
			if (isNaN(volume) || volume < 0) {
				$(this).val('').attr('placeholder', 'Masukkan nilai volume yang valid.');
				$('#vol_benih').val('').prop('disabled', true);
				$('#vol_jual').val('').prop('readonly', true);
			} else {
				$(this).attr('placeholder', '');
				$('#vol_benih').prop('disabled', false);
				$('#vol_jual').prop('readonly', false);
				updateVolJual();
			}
		});

		// Reset vol_jual if vol_benih is changed
		$('#vol_benih').on('input', function() {
			var volBenih = parseFloat($(this).val());
			var volume = parseFloat($('#volume').val());
			if (isNaN(volBenih) || volBenih < 0 || volBenih > volume) {
				$(this).val('').attr('placeholder', 'Masukkan nilai vol benih yang valid.');
			} else {
				$(this).attr('placeholder', '');
				updateVolJual();
			}
		});

		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(
				function(position) {
					console.log("Latitude: " + position.coords.latitude);
					console.log("Longitude: " + position.coords.longitude);

					var thisLat = position.coords.latitude;
					var thisLong = position.coords.longitude;
					$('#latitude').val(thisLat);
					$('#longitude').val(thisLong);
					$('#gpstatus').html('GPS status <span class="text-success font-weight-bold">Aktif</span>');

					initMap(thisLat, thisLong);
				},
				function(error) {
					console.error("Error Code = " + error.code + " - " + error.message);
					$('#gpstatus').html('GPS status <span class="text-danger font-weight-bold">Tidak Aktif/Tidak Diijinkan</span>');
				}
			);
		} else {
			console.log("Geolocation is not supported by this browser.");
			$('#gpstatus').html('Perangkat <span class="text-danger font-weight-bold">Tidak mendukung</span> Fitur ini.');
		}

		var lat = parseFloat('{{$data["spatial"]->latitude}}');
		var lng = parseFloat('{{$data["spatial"]->longitude}}');
		var poly = JSON.parse('{{$data["spatial"]->polygon}}'.replace(/&quot;/g,'"'));
		var kodeId = '{{$data["spatial"]->kode_spatial}}';
		console.log(lat, lng, poly, kodeId);
	});
	let myMap;
	const markers = [];
	let polygon;

	function initMap() {
		myMap = new google.maps.Map(document.getElementById("myMap"), {
			center: { lat: -2.5489, lng: 118.0149 },
			zoom: 5,
			mapTypeId: google.maps.MapTypeId.SATELLITE,
			mapTypeControl: false,
			streetViewControl: false,
			scaleControl: true,
			rotateControl: false,
			styles: [
				{
					featureType: 'all',
					elementType: 'labels',
					stylers: [{ visibility: 'off' }]
				}
			]
		});

		// cameraBtn();
		createMarker();
		createPolygon();
	}

	// function cameraBtn() {
	// 	var controlDiv = document.createElement('div');

	// 	var button = document.createElement('button');
	// 	button.style.backgroundColor = '#fff';
	// 	button.style.border = 'none';
	// 	button.style.outline = 'none';
	// 	button.style.width = '40px';
	// 	button.style.height = '40px';
	// 	button.style.borderRadius = '2px';
	// 	button.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
	// 	button.style.cursor = 'pointer';
	// 	button.style.marginRight = '10px';
	// 	button.style.padding = '0';
	// 	button.title = 'Take a Photo';
	// 	controlDiv.appendChild(button);

	// 	var icon = document.createElement('i');
	// 	icon.className = 'fas fa-camera';
	// 	icon.style.fontSize = '18px';
	// 	icon.style.margin = '10px';
	// 	button.appendChild(icon);

	// 	button.addEventListener('click', function() {
	// 		document.getElementById('cameraInput').click();
	// 	});

	// 	controlDiv.index = 1;
	// 	myMap.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(controlDiv);
	// }

	function createMarker() {
		const latitude = parseFloat('{{$data["spatial"]->latitude}}');
		const longitude = parseFloat('{{$data{"spatial"}->longitude}}');
		const kodeId = '{{$data["spatial"]->kode_spatial}}';

		if (!isNaN(latitude) && !isNaN(longitude)) {
			const position = new google.maps.LatLng(latitude, longitude);
			const marker = new google.maps.Marker({
				position: position,
				map: myMap,
				draggable: false,
				label: {
					text: kodeId,
					color: "white", // Set the label text color to white
					fontSize: "14px", // Optional: Adjust the font size
					fontWeight: "bold" // Optional: Make the label bold
				}
			});
			markers.push(marker);
			myMap.setCenter(position);
			myMap.setZoom(18);
		}
	}

	function createPolygon() {
		let polygonCoords = '{{$data["spatial"]->polygon}}'.replace(/&quot;/g,'"');
		if (polygonCoords !== "") {
			try {
				const parsedCoords = JSON.parse(polygonCoords).map(coord => ({ lat: coord.lat, lng: coord.lng }));
				if (polygon) {
					polygon.setMap(null);
				}
				polygon = new google.maps.Polygon({
					paths: parsedCoords,
					strokeColor: "#0000FF",
					strokeOpacity: 0.8,
					strokeWeight: 2,
					fillColor: "#FF0000",
					fillOpacity: 0.35,
					map: myMap,
					editable: false,
					draggable: false,
				});
				const bounds = new google.maps.LatLngBounds();
				parsedCoords.forEach(point => bounds.extend(point));
				myMap.fitBounds(bounds);
			} catch (e) {
				console.error("Invalid polygon coordinates: ", e);
			}
		}
	}

	window.addEventListener('load', function() {
		initMap();
	});
</script>
<script>
	document.getElementById('camlahan').addEventListener('click', function() {
		document.getElementById('lahanfoto').click();
	});

	document.getElementById('lahanfoto').addEventListener('change', function(event) {
		const file = event.target.files[0];
		if (file) {
			// Update the label with the selected file name
			document.querySelector('.custom-file-label').textContent = file.name;
			// Lakukan sesuatu dengan file yang diambil dari kamera
			console.log(file);
		}
	});
</script>
@endsection
