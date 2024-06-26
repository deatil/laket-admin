/*!
 * lakeAdminTool.js v1.0.3
 * https://github.com/deatil/lake-admin
 * 
 * Apache License 2.0 © Deatil
 */
layui.define(['jquery', 'jqueryCookie', 'lakeAdminMenu'], function (exports) {
    var lakeAdminMenu = layui.lakeAdminMenu,
        $ = layui.$;
    
    var lakeAdminTool = {
        // 选择左边菜单
        selectLeftMenu: function(data_id) {
            // 选择左边菜单
            $("#side_menus_bar").find(".layui-this").removeClass('layui-this');
            $("#side_menus_bar").find("a[lay-id=" + data_id + "]").parent().addClass('layui-this');
            
            if ($("#side_menus_bar").find("a[lay-id=" + data_id + "]").length > 0) {
                var thiz = $("#side_menus_bar").find("a[lay-id=" + data_id + "]");
                
                var childDds = $("#side_menus_bar")
                    .find(".layui-nav-child")
                    .parents('dd');
                if (childDds.length > 0) {
                    $(childDds).each(function() {
                        $(this).addClass('layui-nav-item-active');
                    });
                }
                
                var navItems = thiz.parents('#side_menus_bar .layui-nav-item');
                var navChildItems = thiz.parents('#side_menus_bar .layui-nav-item-active');
                
                $("#side_menus_bar")
                    .find(".layui-nav-item")
                    .removeClass('layui-nav-itemed')
                    .removeClass('layui-nav-item-active');
                
                $(navItems).each(function() {
                    $(this).addClass('layui-nav-itemed')
                        .addClass('layui-nav-item-active');
                });
                $(navChildItems).each(function() {
                    $(this).addClass('layui-nav-itemed')
                        .addClass('layui-nav-item-active');
                });
            }
        },
        
        // 重新刷新页面，使用location.reload()有可能导致重新提交
        reloadPage: function(win) {
            var location = win.location;
            location.href = location.pathname + location.search;
        },

        // 判断显示或创建iframe
        iframeJudge: function(options) {
            var thiz = this;
            var elem = options.elem,
                href = options.href,
                id = options.id,
                li = $('#body_history li[lay-id=' + id + ']');
                
            // 如果iframe标签是已经存在的，则显示并让选项卡高亮,并不显示loading
            if (li.length > 0) {
                var iframe = $('#iframe_' + id);
                if (iframe[0].contentWindow && iframe[0].contentWindow.location.href !== href) {
                    iframe[0].contentWindow.location.href = href;
                }
                $('#body_frame iframe').hide();
                $('#iframe_' + id).show();
                thiz.showTab(li); //计算此tab的位置，如果不在屏幕内，则移动导航位置
            } else {
                // 创建一个并加以标识
                var iframeAttr = {
                    src: href,
                    id: 'iframe_' + id,
                    frameborder: '0',
                    scrolling: 'auto',
                    height: '100%',
                    width: '100%'
                };
                var iframe = $('<iframe/>').prop(iframeAttr).appendTo('#body_frame .layui-tab-content .lake-admin-iframe-box');

                $(iframe[0].contentWindow.document).ready(function() {
                    $('#body_frame iframe').hide();
                
                    var layerLoad = layer.load(1, {
                        shade: false
                    });
                    iframe[0].onload = function () {
                        layer.close(layerLoad);
                    };
                    
                    if (options.icon) {
                        var icon = '<i class="item-dragsort ' + options.icon + '"></i>&nbsp;';
                    } else {
                        var icon = '<i class="item-dragsort icon-neirongguanli"></i>&nbsp;';
                    }
                    
                    var title = icon + '<span class="layui-nav-title">' + options.title + '</span>';
                    var li = $('<li class="layui-tab-item lake-admin-tab-item">' + title + '<i class="layui-icon layui-unselect layui-tab-close">&#x1006;</i></li>').attr('lay-id', id);
                    li.appendTo('#body_history');
                    thiz.showTab(li); //计算此tab的位置，如果不在屏幕内，则移动导航位置
                });
            }
        },

        // 顶部导航时位置判断
        showTabWidth: function(li) {
            if (li.length) {
                var ul = $('#body_history'),
                    li_offset = li.offset(),
                    li_width = li.outerWidth(true),
                    next_left = $('#page-next').offset().left, //右边按钮的界限位置
                    prev_right = $('#page-prev').offset().left + $('#page-prev').outerWidth(true); //左边按钮的界限位置
                if (li_offset.left + li_width > next_left) { //如果将要移动的元素在不可见的右边，则需要移动
                    var distance = li_offset.left + li_width - next_left; //计算当前父元素的右边距离，算出右移多少像素
                    ul.animate({
                        left: '-=' + distance
                    }, 200, 'swing');
                } else if (li_offset.left < prev_right) { //如果将要移动的元素在不可见的左边，则需要移动
                    var distance = prev_right - li_offset.left; //计算当前父元素的左边距离，算出左移多少像素
                    ul.animate({
                        left: '+=' + distance
                    }, 200, 'swing');
                }
            }
        },

        // 显示顶部导航时作位置判断，点击左边菜单、上一tab、下一tab时公用
        showTab: function(li) {
            if (li.length > 0) {
                li.trigger('click');
            }
        }
        
    };
    
    exports('lakeAdminTool', lakeAdminTool);
});
