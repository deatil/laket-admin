/**
 * 快速绑定方法，部分代码来自于网络
 */
layui.define(['table', 'element', 'layer', 'form', 'notice', 'msg', 'lakeform', 'fieldlist'], function(exports) {
    var element = layui.element,
        table = layui.table,
        layer = layui.layer,
        $ = layui.jquery,
        form = layui.form,
        msg = layui.msg,
        lakeform = layui.lakeform,
        notice = layui.notice;
    
    window.$body = $('body');
    
    /*! 顶部鼠标移上显示 */
    var layer_tip;
    $(document).on('mouseenter', '[data-lake-tip]', function() {
        var title = $(this).attr("lay-title");
        if (title) {
            layer_tip = layer.tips(title, this, {
                tips: [1, '#009688'],
            });
        }
    });
    $(document).on('mouseleave', '[data-lake-tip]', function() {
        if (layer_tip) {
            layer.close(layer_tip);
        }
    });
    
    /*! 文件上传集合 */
    var webuploader = [];
    /*! 当前上传对象 */
    var curr_uploader = {};
    
    // 文件上传
    $('.js-upload-file,.js-upload-files').each(function() {
         var $input_file = $(this).find('input');
         var $input_file_name = $input_file.attr('id');
         // 是否多文件上传
         var $multiple = $input_file.data('multiple');
         // 允许上传的后缀
         var $ext = $input_file.data('ext');
         // 文件限制大小
         var $size = $input_file.data('size');
         // 文件列表
         var $file_list = $('#file_list_' + $input_file_name);
         // 实例化上传
         var uploader = WebUploader.create({
             // 选完文件后，是否自动上传。
             auto: true,
             // 去重
             duplicate: true,
             // swf文件路径
             swf: laket.url.webuploader_swf,
             // 文件接收服务端。
             server: laket.url.file_upload_url,
             // 选择文件的按钮。可选。
             // 内部根据当前运行是创建，可能是input元素，也可能是flash.
             pick: {
                 id: '#picker_' + $input_file_name,
                 multiple: $multiple
             },
             // 文件限制大小
             fileSingleSizeLimit: $size,
             // 只允许选择文件文件。
             accept: {
                 title: 'Files',
                 extensions: $ext
             }
         });

         // 当有文件添加进来的时候
         uploader.on('fileQueued', function(file) {
             var $li = '<tr id="' + file.id + '" class="file-item"><td>' + file.name + '</td>' +
                 '<td class="file-state">正在读取文件信息...</td><td><div class="layui-progress"><div class="layui-progress-bar" lay-percent="0%"></div></div></td>' +
                 '<td><a href="javascript:void(0);" class="layui-btn download-file layui-btn layui-btn-xs">下载</a> <a href="javascript:void(0);" class="layui-btn remove-file layui-btn layui-btn-xs layui-btn-danger">删除</a></td></tr>';

             if ($multiple) {
                 $file_list.find('.file-box').append($li);
             } else {
                 $file_list.find('.file-box').html($li);
                 // 清空原来的数据
                 $input_file.val('');
             }
             // 设置当前上传对象
             curr_uploader = uploader;
         });

         // 文件上传成功
         uploader.on('uploadSuccess', function(file, response) {
             var $li = $('#' + file.id);
             if (response.code == 0) {
                 if ($multiple) {
                     if ($input_file.val()) {
                         $input_file.val($input_file.val() + ',' + response.id);
                     } else {
                         $input_file.val(response.id);
                     }
                     $li.find('.remove-file').attr('data-id', response.id);
                 } else {
                     $input_file.val(response.id);
                 }
             }
             // 加入提示信息
             $li.find('.file-state').html('<span class="text-' + response.class + '">' + response.info + '</span>');
             // 添加下载链接
             $li.find('.download-file').attr('href', response.path);
         });

         // 文件上传过程中创建进度条实时显示。
         uploader.on('uploadProgress', function(file, percentage) {
             var $percent = $('#' + file.id).find('.layui-progress-bar');
             $percent.css('width', percentage * 100 + '%');
         });

         // 文件上传失败，显示上传出错。
         uploader.on('uploadError', function(file) {
             var $li = $('#' + file.id);
             $li.find('.file-state').html('<span class="text-danger">服务器发生错误~</span>');
         });

         // 文件验证不通过
         uploader.on('error', function(type) {
             switch (type) {
                 case 'Q_TYPE_DENIED':
                     layer.alert('图片类型不正确，只允许上传后缀名为：' + $ext + '，请重新上传！', { icon: 5 })
                     break;
                 case 'F_EXCEED_SIZE':
                     layer.alert('图片不得超过' + $size + 'kb，请重新上传！', { icon: 5 })
                     break;
             }
         });
         // 删除文件
         $file_list.delegate('.remove-file', 'click', function() {
             if ($multiple) {
                 var id = $(this).data('id'),
                     ids = $input_file.val().split(',');

                 if (id) {
                     for (var i = 0; i < ids.length; i++) {
                         if (ids[i] == id) {
                             ids.splice(i, 1);
                             break;
                         }
                     }
                     $input_file.val(ids.join(','));
                 }
             } else {
                 $input_file.val('');
             }
             $(this).closest('.file-item').remove();
         });
         // 将上传实例存起来
         webuploader.push(uploader);
    });
    
    $('.uploader-list').each(function () {
        $(this).viewer();
    });

    // 图片上传
    $('.js-upload-image,.js-upload-images').each(function() {
         var $input_file = $(this).find('input');
         var $input_file_name = $input_file.attr('id');
         // 图片列表
         var $file_list = $('#file_list_' + $input_file_name);
         // 缩略图参数
         var $thumb = $input_file.data('thumb');
         // 水印参数
         var $watermark = $input_file.data('watermark');
         // 是否多图片上传
         var $multiple = $input_file.data('multiple');
         // 允许上传的后缀
         var $ext = $input_file.data('ext');
         // 图片限制大小
         var $size = $input_file.data('size');
         // 优化retina, 在retina下这个值是2
         var ratio = window.devicePixelRatio || 1;
         // 缩略图大小
         var thumbnailWidth = 100 * ratio;
         var thumbnailHeight = 100 * ratio;

         var uploader = WebUploader.create({
             // 选完图片后，是否自动上传。
             auto: true,
             // 去重
             duplicate: true,
             // 不压缩图片
             resize: false,
             compress: false,
             // swf文件路径
             swf: laket.url.webuploader_swf,
             pick: {
                 id: '#picker_' + $input_file_name,
                 multiple: $multiple
             },
             server: laket.url.image_upload_url,
             // 图片限制大小
             fileSingleSizeLimit: $size,
             // 只允许选择图片文件。
             accept: {
                 title: 'Images',
                 extensions: $ext,
                 mimeTypes: 'image/jpg,image/jpeg,image/bmp,image/png,image/gif'
             },
             // 自定义参数
             formData: {
                 thumb: $thumb,
                 watermark: $watermark
             }

         })

         // 当有文件添加进来的时候
         uploader.on('fileQueued', function(file) {
             var $li = $(
                     '<div id="' + file.id + '" class="file-item js-gallery thumbnail">' +
                     '<img>' +
                     '<div class="info">' + file.name + '</div>' +
                     '<i class="iconfont icon-delete_fill remove-picture"></i>' +
                     ($multiple ? '<i class="iconfont icon-yidong move-picture"></i>' : '') +
                     '<div class="progress progress-mini remove-margin active">' +
                     '<div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>' +
                     '</div>' +
                     '<div class="file-state img-state"><div class="layui-bg-blue">正在读取...</div>' +
                     '</div>'
                 ),
                 $img = $li.find('img');

             if ($multiple) {
                 $file_list.append($li);
             } else {
                 $file_list.html($li);
                 $input_file.val('');
             }
             // 创建缩略图
             // 如果为非图片文件，可以不用调用此方法。
             // thumbnailWidth x thumbnailHeight 为 100 x 100
             uploader.makeThumb(file, function(error, src) {
                 if (error) {
                     $img.replaceWith('<span>不能预览</span>');
                     return;
                 }
                 $img.attr('src', src);
             }, thumbnailWidth, thumbnailHeight);
             // 设置当前上传对象
             curr_uploader = uploader;
         });

         // 文件上传过程中创建进度条实时显示。
         uploader.on('uploadProgress', function(file, percentage) {
             var $percent = $('#' + file.id).find('.progress-bar');
             //console.log($percent);
             $percent.css('width', percentage * 100 + '%');
         });

         // 文件上传成功
         uploader.on('uploadSuccess', function(file, response) {
             var $li = $('#' + file.id);
             if (response.code == 0) {
                 if ($multiple) {
                     if ($input_file.val()) {
                         $input_file.val($input_file.val() + ',' + response.id);
                     } else {
                         $input_file.val(response.id);
                     }
                     $li.find('.remove-picture').attr('data-id', response.id);
                 } else {
                     $input_file.val(response.id);
                 }
             }
             $li.find('.file-state').html('<div class="layui-bg-green">' + response.info + '</div>');
             $li.find('img').attr('data-original', response.path);
             // 上传成功后，再次初始化图片查看功能
            $('.uploader-list').each(function () {
                $(this).viewer();
            });
         });

         // 文件上传失败，显示上传出错。
         uploader.on('uploadError', function(file) {
             var $li = $('#' + file.id);
             $li.find('.file-state').html('<div class="layui-bg-red">服务器错误</div>');
         });

         // 文件验证不通过
         uploader.on('error', function(type) {
             switch (type) {
                 case 'Q_TYPE_DENIED':
                     layer.alert('图片类型不正确，只允许上传后缀名为：' + $ext + '，请重新上传！', { icon: 5 })
                     break;
                 case 'F_EXCEED_SIZE':
                     layer.alert('图片不得超过' + $size + 'kb，请重新上传！', { icon: 5 })
                     break;
             }
         });

         // 完成上传完了，成功或者失败，先删除进度条。
         uploader.on('uploadComplete', function(file) {
             setTimeout(function() {
                 $('#' + file.id).find('.progress').remove();
             }, 500);

         });

         // 删除图片
         $file_list.delegate('.remove-picture', 'click', function() {
             $(this).closest('.file-item').remove();
             if ($multiple) {
                 var ids = [];
                 $file_list.find('.remove-picture').each(function() {
                     ids.push($(this).data('id'));
                 });
                 $input_file.val(ids.join(','));
             } else {
                 $input_file.val('');
             }
             // 删除后，再次初始化图片查看功能
            $('.uploader-list').each(function () {
                $(this).viewer();
            });
         });

         // 将上传实例存起来
         webuploader.push(uploader);
         // 如果是多图上传，则实例化拖拽
         if ($multiple) {
             Sortable.create($file_list.get(0), {
                 group: "file_list",
                 handle: '.move-picture',
                 animation: 150,
                 ghostClass: "sortable-ghost",
                 onEnd: function(evt) {
                     var ids = [];
                     $file_list.find('.remove-picture').each(function() {
                         ids.push($(this).data('id'));
                     });
                     $input_file.val(ids.join(','));
                 }
             });
         }
    });
    
    /*! 多参数表单 */
    $('.layui-form-fieldlist').fieldlist();
    
    /*! 注册 data-lake-load 事件行为 */
    $body.on('click', '[data-lake-load]', function () {
        var url = $(this).attr('data-lake-load'), tips = $(this).attr('data-lake-tips'), time = $(this).attr('data-lake-time');
        if ($(this).attr('data-lake-confirm')) return msg.confirm($(this).attr('data-lake-confirm'), function () {
            lakeform.load(url, {}, 'get', null, true, tips, time);
        });
        lakeform.load(url, {}, 'get', null, true, tips, time);
    });

    /*! 注册 data-lake-modal 事件行为 */
    $body.on('click', '[data-lake-modal]', function () {
        return lakeform.modal($(this).attr('data-lake-modal'), 'open_type=modal', $(this).attr('data-lake-title') || $(this).text() || '编辑');
    });

    /*! 注册 data-lake-open 事件行为 */
    $body.on('click', '[data-lake-open]', function () {
        lakeform.href($(this).attr('data-lake-open'), this);
    });

    /*! 注册 data-lake-dbclick 事件行为 */
    $body.on('dblclick', '[data-lake-dbclick]', function () {
        $(this).find(this.getAttribute('data-lake-dbclick') || '[data-lake-dbclick]').trigger('click');
    });

    /*! 注册 data-lake-reload 事件行为 */
    $body.on('click', '[data-lake-reload]', function () {
        lakeform.reload();
    });

    /*! 注册 data-lake-check 事件行为 */
    $body.on('click', '[data-lake-check-target]', function () {
        var checked = !!this.checked;
        $($(this).attr('data-lake-check-target')).map(function () {
            this.checked = checked;
            $(this).trigger('change');
        });
    });

    /*! 注册 data-lake-action 事件行为 */
    $body.on('click', '[data-lake-action]', function () {
        var $this = $(this), data = {}, time = $this.attr('data-lake-time'), action = $this.attr('data-lake-action');
        var loading = $this.attr('data-lake-loading'), method = $this.attr('data-lake-method') || 'post';
        var rule = $this.attr('data-lake-value') || (function (rule, ids) {
            $($this.attr('data-lake-target') || 'input[type=checkbox].list-check-box').map(function () {
                (this.checked) && ids.push(this.value);
            });
            return ids.length > 0 ? rule.replace('{key}', ids.join(',')) : '';
        }).call(this, $this.attr('data-lake-rule') || '', []) || '';
        if (rule.length < 1) return msg.tips('请选择需要更改的数据！');
        var rules = rule.split(';');
        for (var i in rules) {
            if (rules[i].length < 2) return msg.tips('异常的数据操作规则，请修改规则！');
            data[rules[i].split('#')[0]] = rules[i].split('#')[1];
        }
        data['_csrf_'] = $this.attr('data-lake-token') || $this.attr('data-lake-csrf') || '--';
        var load = loading !== 'false', tips = typeof loading === 'string' ? loading : undefined;
        if (!$this.attr('data-lake-confirm')) lakeform.load(action, data, method, false, load, tips, time);
        else msg.confirm($this.attr('data-lake-confirm'), function () {
            lakeform.load(action, data, method, false, load, tips, time);
        });
    });

    /*! 输入框失焦提交 */
    $body.on('blur', '[data-lake-action-blur]', function () {
        var data = {}, that = this, $this = $(this), action = $this.attr('data-lake-action-blur');
        var time = $this.attr('data-lake-time'), loading = $this.attr('data-lake-loading') || false;
        var load = loading !== 'false', tips = typeof loading === 'string' ? loading : undefined;
        var method = $this.attr('data-lake-method') || 'post', confirm = $this.attr('data-lake-confirm');
        var attrs = $this.attr('data-lake-value').replace('{value}', $this.val()).split(';');
        for (var i in attrs) {
            if (attrs[i].length < 2) return msg.tips('异常的数据操作规则，请修改规则！');
            data[attrs[i].split('#')[0]] = attrs[i].split('#')[1];
        }
        that.callback = function (ret) {
            $this.css('border', (ret && ret.code) ? '1px solid #e6e6e6' : '1px solid red');
            return false;
        };
        data['_csrf_'] = $this.attr('data-lake-token') || $this.attr('data-lake-csrf') || '--';
        if (!confirm) return lakeform.load(action, data, method, that.callback, load, tips, time);
        msg.confirm(confirm, function () {
            lakeform.load(action, data, method, that.callback, load, tips, time);
        });
    });

    /*! 注册 data-lake-href 事件行为 */
    $body.on('click', '[data-lake-href]', function (href) {
        href = $(this).attr('data-lake-href');
        if (href && href.indexOf('#') !== 0) window.location.href = href;
    });

    /*! 注册 data-lake-iframe 事件行为 */
    $body.on('click', '[data-lake-iframe]', function (index) {
        index = lakeform.iframe($(this).attr('data-lake-iframe'), $(this).attr('data-lake-title') || '窗口');
        $(this).attr('data-lake-index', index);
    });

    /*! 注册 data-lake-copy 事件行为 */
    $body.on('click', '[data-lake-copy]', function () {
        $.copyToClipboard(this.getAttribute('data-lake-copy'));
    });
    $.copyToClipboard = function (content, input) {
        input = document.createElement('textarea');
        input.style.position = 'absolute', input.style.left = '-100000px';
        input.style.width = '1px', input.style.height = '1px', input.innerText = content;
        document.body.appendChild(input), input.select(), setTimeout(function () {
            document.execCommand('Copy') ? msg.tips('复制成功') : msg.tips('复制失败，请使用鼠标操作复制！');
            document.body.removeChild(input);
        }, 100);
    };

    /*! 注册 data-lake-tips-text 事件行为 */
    $body.on('mouseenter', '[data-lake-tips-text]', function () {
        $(this).attr('index', layer.tips($(this).attr('data-lake-tips-text'), this, {tips: [$(this).attr('data-lake-tips-type') || 3, '#78BA32']}));
    }).on('mouseleave', '[data-lake-tips-text]', function () {
        layer.close($(this).attr('index'));
    });

    /*! 注册 data-lake-tips-image 事件行为 */
    $body.on('click', '[data-lake-tips-image]', function () {
        $.previewImage(this.getAttribute('data-lake-tips-image') || this.src, this.getAttribute('data-lake-width'));
    });
    $.previewImage = function (src, area) {
        var img = new Image(), index = msg.loading();
        img.style.background = '#fff', img.style.display = 'none';
        img.style.height = 'auto', img.style.width = area || '480px';
        document.body.appendChild(img), img.onerror = function () {
            msg.close(index);
        }, img.onload = function () {
            layer.open({
                type: 1, shadeClose: true, success: img.onerror, content: $(img), title: false,
                area: area || '480px', closeBtn: 1, skin: 'layui-layer-nobg', end: function () {
                    document.body.removeChild(img);
                }
            });
        };
        img.src = src;
    };

    /*! 注册 data-lake-phone-view 事件行为 */
    $body.on('click', '[data-lake-phone-view]', function () {
        $.previewPhonePage(this.getAttribute('data-lake-phone-view') || this.href);
    });
    $.previewPhonePage = function (href, title) {
        var tpl = '<div><div class="mobile-preview pull-left"><div class="mobile-header">_TITLE_</div><div class="mobile-body"><iframe id="phone-preview" src="_URL_" frameborder="0" marginheight="0" marginwidth="0"></iframe></div></div></div>';
        layer.style(layer.open({type: true, scrollbar: false, area: ['320px', '600px'], title: false, closeBtn: true, shadeClose: false, skin: 'layui-layer-nobg', content: $(tpl.replace('_TITLE_', title || '公众号').replace('_URL_', href)).html(),}), {boxShadow: 'none'});
    };

    /*! 表单编辑返回操作 */
    $body.on('click', '[data-lake-history-back]', function (title) {
        title = this.getAttribute('data-lake-history-back') || '确定要返回上一页吗？';
        msg.confirm(title, function (index) {
            history.back();
            msg.close(index);
        })
    });

    /*! 表单元素失去焦点处理 */
    $body.on('blur', '[data-lake-blur-number]', function (fiexd) {
        fiexd = this.getAttribute('data-lake-blur-number') || 0;
        this.value = (parseFloat(this.value) || 0).toFixed(fiexd);
    });
    
    exports('lakeBind', {});
});