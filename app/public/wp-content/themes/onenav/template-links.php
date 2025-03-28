<?php
/*
Template Name: 友情链接
*/
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();  
$col = get_post_meta( get_the_ID(), 'sidebar_layout', true )!="sidebar_no"?"col-md-4":"col-md-3";
?>
<style type="text/css"> 
h3{font-size: 1.1rem;margin-top: 20px}
.contextual-callout  p{font-size: 13px;}
.contextual-callout {background-color: rgba(241, 64, 75, 0.12);color: #f1404b!important;padding: 15px;margin: 10px 0 20px;border: 1px solid rgba(241, 64, 75, 0.07);border-left-width: 4px;border-radius: 3px;font-size: 1.3rem;line-height: 1.5;border-left-color: #f1404b;}
.contextual-callout>h4 {margin-bottom: 16px;text-align: center;font-size: 1rem;color:#f1404b}
.link-header h1 {font-size: 16px;font-size: 1.6rem;line-height: 30px;text-align: center;margin: 0 0 15px 0;}
.link-page {margin: 30px 0; } 
</style>
	<div id="content" class="container my-4 my-md-5"> 
        <div class="content-wrap">
            <div class="content-layout">
				<h1 class="h3 mb-4"><?php echo get_the_title() ?>
				<?php edit_post_link('<i class="iconfont icon-modify mr-1"></i>'.__('编辑','i_theme'), '<span class="edit-link text-xs text-muted">', '</span>' ); ?>
				</h1>
				<div class="content page">
					<div class="single-content panel-body">
						<?php if(get_post_meta( get_the_ID(), '_disable_links_content', true )){ ?>
						<p>一、申请友链可以直接在本页面留言，内容包括网站名称、链接以及相关说明，为了节约你我的时间，可先做好本站链接并此处留言，我将尽快答复</p>
						<p>二、欢迎申请友情链接，只要是正规站常更新即可，申请首页链接需符合以下几点要求：</p>
						<ul>
							<li>本站优先招同类原创、内容相近的博客或网站；</li>
							<li>Baidu和Google有正常收录，百度近期快照，不含有违反国家法律内容的合法网站，TB客，垃圾站不做。</li>
							<li>如果您的站原创内容少之又少，且长期不更新，申请连接不予受理！</li>
							<li>友情链接的目的是常来常往，凡是加了友链的朋友，我都会经常访问的，也欢迎你来我的网站参观、留言等。</li>
						</ul>
						<p>长期不更新的会视情节把友链转移至内页。</p>
						<div class=" contextual-callout">
							<h4>友链申请示例</h4>
							<p>本站名称：<?php echo get_bloginfo('name') ?><br>
							本站链接：<?php echo esc_url(home_url()) ?><br>
							本站描述：<?php echo get_bloginfo('description') ?><br>
							本站图标：<?php echo io_get_option('favicon','') ?></p>
						</div>
						<p>PS:链接由于无法访问或您的博客没有发现本站链接等其他原因，将会暂时撤销超链接，恢复请留言通知我，望请谅解，谢谢！</p>
						<?php } ?>
						<?php the_content(); ?>
					</div> <!-- .single-content -->  
					<div class="link-page">
						<?php $default_ico = get_template_directory_uri() .'/images/favicon.png'; 
						$linkcats = get_terms( 'link_category' );
						if ( !empty( $linkcats ) ) {
							foreach( $linkcats as $linkcat ){
								echo '<div class="link-title mb-3"><h3 class="link-cat"><i class="site-tag iconfont icon-tag icon-lg mr-1"></i>'.$linkcat->name.'</h3></div>';
								$bookmarks = get_bookmarks(array(
									'orderby' => 'rating',
									'order' => 'asc',
									'category'  => $linkcat->term_id,
								));
								echo'<div class="row">';
								foreach ($bookmarks as $bookmark) { 
									$ico = $bookmark->link_image?:io_get_option('ico-source','https://api.iowen.cn/favicon/','ico_url') .format_url($bookmark->link_url) . io_get_option('ico-source','.png','ico_png');
								?>
								<div class="url-card col-6 <?php echo $col ?>"> 
									<div class="card url-body default">	
										<div class="card-body">
											<div class="url-content d-flex align-items-center"> 
												<div class="url-img rounded-circle mr-2 d-flex align-items-center justify-content-center">
													<?php if(io_get_option('lazyload',false)): ?>
													<img class="lazy" src="<?php echo $default_ico; ?>" data-src="<?php echo $ico ?>">
													<?php else: ?>
													<img class="" src="<?php echo $ico ?>">
													<?php endif ?>
												</div> 
												<div class="url-info flex-fill">
													<div class="text-sm overflowClip_1">
														<a href="<?php echo $bookmark->link_url; ?>" title="<?php echo $bookmark->link_name; ?>" target="_blank"><strong><?php echo $bookmark->link_name; ?></strong></a>
													</div>
													<p class="overflowClip_1 m-0 text-xs"><?php echo $bookmark->link_description ?></p>
												</div>
											</div>
										</div>
									</div>
								</div>
								<?php }
								echo'</div>';
							}
						} else {
							echo'<div class="row">';
							$bookmarks = get_bookmarks(array(
								'orderby' => 'rating',
								'order' => 'asc'
							));
							foreach ($bookmarks as $bookmark) {  
								$ico = $bookmark->link_image?:io_get_option('ico-source','https://api.iowen.cn/favicon/','ico_url') .format_url($bookmark->link_url) . io_get_option('ico-source','.png','ico_png');
							?>
								<div class="url-card col-6 <?php echo $col ?>"> 
									<div class="card url-body default">	
										<div class="card-body">
											<div class="url-content d-flex align-items-center"> 
												<div class="url-img rounded-circle mr-2 d-flex align-items-center justify-content-center">
													<?php if(io_get_option('lazyload',false)): ?>
													<img class="lazy" src="<?php echo $default_ico; ?>" data-src="<?php echo $ico ?>">
													<?php else: ?>
													<img class="" src="<?php echo $ico ?>">
													<?php endif ?>
												</div> 
												<div class="url-info flex-fill">
													<div class="text-sm overflowClip_1">
														<a href="<?php echo $bookmark->link_url; ?>" title="<?php echo $bookmark->link_name; ?>" target="_blank"><strong><?php echo $bookmark->link_name; ?></strong></a>
													</div>
													<p class="overflowClip_1 m-0 text-xs"><?php echo $bookmark->link_description ?></p>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php }
							echo'</div>';
						} ?>
						<div class="clear"></div>
					</div> 
					<?php if(get_post_meta( get_the_ID(), '_links_form', true )){ ?>  
					<h2 class="text-gray text-lg my-4"><i class="iconfont icon-diandian mr-1"></i>提交链接</h2>
					<div class="card">
						<div class="card-body">
							<form method="post" class="io-add-link-form only-submit">
									
								<div class="form-row">
									<div class="form-group col-md-6">
										<label for="link_name">链接名称</label>
										<input type="text" size="40" class="form-control" id="link_name" name="link_name" placeholder="请输入链接名称" />
									</div>
									<div class="form-group col-md-6">
										<label for="link_url">链接地址</label>
										<input type="text" size="40" class="form-control" id="link_url" name="link_url" placeholder="请输入链接地址" />
									</div>
								</div>  
								<div class="form-row">
									<div class="form-group col-md-6">
										<label for="link_description">链接简介</label>
										<input type="text" size="40" class="form-control" id="link_description" name="link_description" placeholder="请输入链接简介" />
									</div>
									<div class="form-group col-md-6">
										<label for="link_image">LOGO地址</label>
										<input type="text" size="40" class="form-control" id="link_image" name="link_image" placeholder="请输入LOGO图像地址" />
									</div>
								</div> 
								<div class=" d-flex justify-content-end flex-wrap">  
								<input type="hidden" name="action" value="io_submit_link"> 
								<?php echo get_captcha_input_html('io_submit_link') ?>
								<button type="reset" class="btn btn-light d-none d-md-block ml-2">重填</button>
								<button type="submit" id="submit" class="btn btn-dark ml-2">提交申请</button>
								</div>
							</form> <!--表单结束-->
						</div>
					</div>
					<?php } ?>
				</div><!-- .content -->
				<?php if ( comments_open() || get_comments_number() ) : ?>
				<?php comments_template( '', true ); ?>
				<?php endif; ?> 	 
            </div>
        </div>
        <?php get_sidebar(); ?>
	</div>
<?php get_footer();
