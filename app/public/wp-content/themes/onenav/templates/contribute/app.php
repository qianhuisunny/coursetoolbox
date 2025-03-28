<?php
/*
 * @Author: iowen
 * @Author URI: https://www.iowen.cn/
 * @Date: 2022-07-07 12:21:04
 * @LastEditors: iowen
 * @LastEditTime: 2022-07-08 02:42:35
 * @FilePath: \onenav\templates\contribute\app.php
 * @Description: 
 */
// TODO:待完善
?>

<div id="down" class="tab-pane fade">
                    <form class="i-tougao" method="post" data-type="down" action="<?php echo esc_url($_SERVER["REQUEST_URI"])?>">
                        <input type="hidden" class="form-control" value="down" name="tougao_type"/>
                        <?php if(io_get_option('sites_tg_opt',0,'img_size')){ ?>
                        <div class="my-2">
                            <label for="tougao_down_ico"><?php _e('资源图标:','i_theme') ?></label>
                            <input type="hidden" value="" id="tougao_down_ico" class="tougao-down" name="tougao_sites_ico" />
                            <div class="upload_img">
                                <div class="show_ico">
                                    <img id="show_down_ico" class="show-down" src="<?php echo get_theme_file_uri('/images/add.png') ?>" alt="<?php _e('网站图标','i_theme') ?>">
                                    <i id="remove_down_ico" class="iconfont icon-close-circle remove-ico remove-down" data-id="" data-type="ico" style="display: none;"></i>
                                </div> 
                                <input type="file" id="upload_down_ico" class="upload-down" name="tougao_ico" data-type="ico" accept="image/*" onchange="uploadImg(this)" >
                            </div>
                        </div>
                        <?php } ?>
                        <div class="row row-sm">
                            <div class="col-sm-6 my-2"> 
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="iconfont icon-name icon-fw" aria-hidden="true"></i></span>
                                    </div>
                                    <input type="text" class="form-control" value="" id="tougao_title" name="tougao_title" placeholder="<?php _e('资源名称 *','i_theme') ?>" maxlength="30"/>
                                </div>
                            </div>
                            <div class="col-sm-6 my-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="iconfont icon-tishi icon-fw" aria-hidden="true"></i></span>
                                    </div>
                                    <input type="text" class="form-control" value="" id="tougao_sites_sescribe" name="tougao_sites_sescribe"  placeholder="<?php _e('资源描述 *','i_theme') ?>" maxlength="50"/>
                                </div>
                            </div>
                            <div class="col-sm-6 my-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="iconfont icon-version icon-fw" aria-hidden="true"></i></span>
                                    </div>
                                    <input type="text" class="form-control" value="" name="tougao_down_version"  placeholder="<?php _e('资源版本','i_theme') ?>" maxlength="50"/>
                                </div>
                            </div>
                            <div class="col-sm-6 my-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="iconfont icon-url icon-fw" aria-hidden="true"></i></span>
                                    </div>
                                    <input type="text" class="form-control" value="" name="tougao_down_formal"  placeholder="<?php _e('官网链接','i_theme') ?>" maxlength="50"/>
                                </div>
                            </div>
                            <div class="col-sm-6 my-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="iconfont icon-url icon-fw" aria-hidden="true"></i></span>
                                    </div>
                                    <input type="text" class="form-control" value="" name="tougao_sites_down"  placeholder="<?php _e('网盘链接','i_theme') ?>" maxlength="50"/>
                                </div>
                            </div>
                            <div class="col-sm-6 my-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="iconfont icon-url icon-fw" aria-hidden="true"></i></span>
                                    </div>
                                    <input type="text" class="form-control" value="" name="tougao_down_preview"  placeholder="<?php _e('演示链接','i_theme') ?>" maxlength="50"/>
                                </div>
                            </div>
                            <div class="col-sm-6 my-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="iconfont icon-password icon-fw" aria-hidden="true"></i></span>
                                    </div>
                                    <input type="text" class="form-control" value="" name="tougao_sites_password"  placeholder="<?php _e('网盘密码','i_theme') ?>" maxlength="50"/>
                                </div>
                            </div>
                            <div class="col-sm-6 my-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="iconfont icon-password icon-fw" aria-hidden="true"></i></span>
                                    </div>
                                    <input type="text" class="form-control" value="" name="tougao_down_decompression"  placeholder="<?php _e('解压密码','i_theme') ?>" maxlength="50"/>
                                </div>
                            </div>
                            <div class="col-sm-6 my-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="iconfont icon-category icon-fw" aria-hidden="true"></i></span>
                                    </div>
                                    <?php
                                    $cat_args = array(
                                        'show_option_all'     => __('选择分类 *','i_theme'),
                                        'hide_empty'          => 0,
                                        'id'                  => 'tougaocategorg_down',
                                        'taxonomy'            => 'favorites',
                                        'name'                => 'tougao_cat',
                                        'class'               => 'form-control',
                                        'show_count'          => 1,
                                        'include'             => io_get_option('sites_tg_opt',0,'cat'),
                                        'hierarchical'        => 1,
                                    );
                                    wp_dropdown_categories($cat_args);
                                    ?>
                                </div>
                            </div>
                            <div class="col-12 my-2">
                                <label style="vertical-align:top" for="tougao_content"><?php _e('资源介绍(使用说明):','i_theme') ?></label>
                                <textarea class="form-control text-sm" rows="6" cols="55" name="tougao_content"></textarea>
                            </div>
                        </div> 
                    </form> 
                </div>
