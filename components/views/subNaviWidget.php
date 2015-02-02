<div class="row hide-on-print">
	<div class="page_wrapper subnavi">
		<div class="twelve columns">
			<div class="subnavi_box">
				<div class="megamenu_container megamenu_light_bar megamenu_light">
					<ul class="megamenu" id="submenue">
            <li class="megamenu_button"><a><span>≡</span></a></li>
						<?php
						$erstes = 'first-button';
						foreach($this->subnavi as $name => $link) {
							$path = Yii::app()->request->getPathInfo();
							$path = substr($path, strpos($path, '/'));
							$aktiv = (stripos($link[0], $path) === false) ? '' : 'aktiv';
							$edit = $name == 'EDIT' ? 'subnavi-edit' : '';
						?>
						<li class="<?php echo $erstes.' '.$aktiv.' '.$edit; ?>">
							<a href="<?php echo $link[0]; ?>" class="megamenu<?php echo !empty($link[1]) ? '_drop' : '';?>"><?php echo $name; ?></a>
							<?php
							if (!empty($link[1])) {
							?>
							<div class="dropdown_fullwidth">
								<div class="dropdown_<?php echo empty($link[2]) ? 12 : 9; ?>columns">
									<?php
									foreach($link[1] as $subtitel => $sublinks) {
									?>
									<div class="col_3">
										<?php if (!is_numeric($subtitel)) {?> 
										<h3><?php echo $subtitel; ?></h3>
										<?php } ?> 
										<ul class="list_unstyled">
											<?php
											foreach($sublinks as $subname => $sublink) {
											?>
											<li><a href="<?php echo $sublink; ?>"><?php echo $subname; ?></a></li>
											<?php
											}
											?>
										</ul>
									</div>
									<?php
									}
									?>
								</div>
								<?php
								if(!empty($link[2])) {
								?>
								<div class="dropdown_3columns hide-for-small"><?php echo $link[2]; ?></div>
								<?php
								}
								?>
							</div>
							<?php
							}
							?>
						</li>
						<?php
							$erstes = '';
						}
						?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>