/*!
 * lakeAdminSkin.js v1.0.5
 * https://github.com/deatil/laket-admin
 * 
 * Apache License 2.0 © Deatil
 */
!(function(a){
    layui.define(['jquery', 'jqueryCookie', "layer"], function (exports) {
        var jquery = layui.$,
            layer = layui.layer;
        
        exports('lakeAdminSkin', a(jquery, layer));
    });
})(function($, layer) {
    
    // 主题
    var lakeAdminSkin = {
        /* 设置主题 */
        changeTheme: function (theme) {
            try {
                this.removeTheme(self);
                (theme && self.layui) && self.layui.link(this.getThemeDir() + theme + this.getCssSuffix(), theme);
                
                this.frameThemeLink(self.window, theme)
            } catch (e) {
            }
        },
        
        /* 移除主题 */
        removeTheme: function (w) {
            if (!w) {
                w = window;
            }
            if (w.layui) {
                var themeId = 'layuicss-theme';
                w.layui.jquery('link[id^="' + themeId + '"]').remove();
            }
        },
        
        /* 主题样式 */
        frameThemeLink: function (w, theme) {
            try {
                var ifs = w.frames;
                for (var i = 0; i < ifs.length; i++) {
                    try {
                        var tif = ifs[i];
                        this.removeTheme(tif);
                        if (theme && tif.layui) {
                            tif.layui.link(this.getThemeDir() + theme + this.getCssSuffix(), theme);
                        }
                        
                        // 子级
                        this.frameThemeLink(tif, theme);
                    } catch (e) {
                    }
                }
            } catch (e) {
            }
        },
        
        /* 获取主题文件后缀 */
        getCssSuffix: function () {
            var cssSuffix = '.css';
            if (layui.cache.version != undefined) {
                cssSuffix += '?v=';
                if (layui.cache.version == true) {
                    cssSuffix += new Date().getTime();
                } else {
                    cssSuffix += layui.cache.version;
                }
            }
            return cssSuffix;
        },
        
        /* 获取主题目录 */
        getThemeDir: function () {
            return layui.cache.base + 'lake-admin/theme/';
        },
        
        /* 皮肤按钮 */
        listenSkinBtn: function() {
            var data = $.cookie('lake-admin-skin');
            var skin = (data != null) ? data : "theme-black";
            
            return skin;
        },
        
        /* 皮肤 */
        listenSkin: function() {
            var skin = this.listenSkinBtn();
            
            if (skin) {
                this.changeTheme(skin);
            }
        },
        
        /* 生成皮肤页面 */
        buildSkinHtml: function () {
            var skin = this.listenSkinBtn();
            
            var skins = [
                {
                    name: 'theme-black', 
                    title: '黑色', 
                    headerLogoBg: '#212225', 
                    headerRightBg: '#212225',
                    leftMenuBg: '#393d49', 
                },
                {
                    name: 'theme-gray', 
                    title: '灰白', 
                    headerLogoBg: '#808080', 
                    headerRightBg: '#f3f2f2',
                    leftMenuBg: '#808080', 
                },
                {
                    name: 'theme-blue', 
                    title: '蓝色', 
                    headerLogoBg: '#3c8dbc', 
                    headerRightBg: '#3c8dbc',
                    leftMenuBg: '#393d49', 
                },
                {
                    name: 'theme-purple', 
                    title: '紫色', 
                    headerLogoBg: '#722ed1', 
                    headerRightBg: '#722ed1',
                    leftMenuBg: '#393d49', 
                },
                {
                    name: 'theme-green', 
                    title: '黑绿', 
                    headerLogoBg: '#00a65a', 
                    headerRightBg: '#00a65a',
                    leftMenuBg: '#393d49', 
                },
                {
                    name: 'theme-red', 
                    title: '红色', 
                    headerLogoBg: '#dd4b39', 
                    headerRightBg: '#dd4b39',
                    leftMenuBg: '#393d49', 
                },
                {
                    name: 'theme-red-white', 
                    title: '红白', 
                    headerLogoBg: '#dd4b39', 
                    headerRightBg: '#dd4b39',
                    leftMenuBg: '#f3f2f2', 
                },
                {
                    name: 'theme-pink', 
                    title: '粉红', 
                    headerLogoBg: '#fb7299', 
                    headerRightBg: '#f3f2f2',
                    leftMenuBg: '#fb7299', 
                },
            ];
            
            var html = '';
            $.each(skins, function (key, val) {
                if (val.name === skin) {
                    html += '<li class="layui-this" data-select-skin title="' + val.title + '" data-skin="' + val.name + '">\n';
                } else {
                    html += '<li data-select-skin title="' + val.title + '" data-skin="' + val.name + '">\n';
                }
                
                html += '<a href="javascript:;" style="" class="clearfix full-opacity-hover">\n' +
                    '<div><span style="display:block; width: 20%; float: left; height: 12px; background: ' + val.headerLogoBg + ';"></span><span style="display:block; width: 80%; float: left; height: 12px; background: ' + val.headerRightBg + ';"></span></div>\n' +
                    '<div><span style="display:block; width: 20%; float: left; height: 40px; background: ' + val.leftMenuBg + ';"></span><span style="display:block; width: 80%; float: left; height: 40px; background: #ffffff;"></span></div>\n' +
                    '</a>\n' +
                    '</li>';
            });
            
            return html;
        },

        listen: function() {
            var thiz = this;
            this.listenSkin();
            
            $('body').on('click', '[data-skin]', function () {
                var loading = layer.load(0, {shade: false, time: 2 * 1000});
                var clientHeight = (document.documentElement.clientHeight) - 60;
                var skinHtml = thiz.buildSkinHtml();
                
                var html = '<div class="laket-admin-color">\n' +
                    '<div class="color-title">\n' +
                    '<span>主题方案</span>\n' +
                    '</div>\n' +
                    '<div class="color-content">\n' +
                    '<ul>\n' + skinHtml + '</ul>\n' +
                    '</div>\n' +
                    '</div>';
                
                layer.open({
                    type: 1,
                    title: false,
                    closeBtn: 0,
                    shade: 0.2,
                    anim: 2,
                    shadeClose: true,
                    id: 'laket-admin-skins',
                    area: ['340px', clientHeight + 'px'],
                    offset: 'rb',
                    content: html,
                    success: function (index, layero) {
                    },
                    end: function () {
                        $('.laket-admin-select-skin').removeClass('layui-this');
                    }
                });
                
                layer.close(loading);
            });
            
            // 监听顶部右侧皮肤
            $(document).on('click', '[data-select-skin]', function (elem) {
                // 修改skin
                if ($(this).attr('data-skin')) {
                    var skin = $(this).attr('data-skin');
                    
                    $('.laket-admin-color .color-content ul .layui-this').removeClass('layui-this');
                    $(this).addClass('layui-this');

                    $.cookie("lake-admin-skin", "", {expires: -1});
                    $.cookie('lake-admin-skin', skin, {
                        expires: 10,
                        path: '/'
                    });
                    
                    thiz.listenSkin();
                }
            });
        }
    };
    
    return lakeAdminSkin;
});
