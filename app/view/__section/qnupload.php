<div id="qnupload-container">
    <label>
        <span class="label-title">
            <button id="pickfiles"><?= L('SELECT_FILE') ?></button>
        </span>

        <span id="upload-progress-container">
            <span id="upload-progress"></span>
        </span>

        <i class="text-ok">
            <small class="invisible-default"><?= L('UPLOAD_OK') ?></small>
        </i>
    </label>

    <span class="vertical-2"></span>

    <div class="invisible-default">
        <textarea class="upload-res" id="upload-res"></textarea>
    </div>

    <form class="invisible-default" method="POST">
        <?= csrf_feild() ?>
        <input type="hidden" name="filekey">
        <label>
            <span class="label-title"><?= L('FILENAME') ?></span>
            <input type="text" name="filename">
        </label>
        <label>
            <span class="label-title"></span>
            <input
            value="<?= L('SAVE_UPLOAD_FILE'), ' (', L('OPTIONAL'), ')' ?>"
            type="submit">
        </label>
    </form>

    <!-- <button id="upload" class="btn-default">
        <span>确认上传</span>
    </button>

    <button id="stop-upload" class="btn-danger">
        <span>暂停上传</span>
    </button> -->
</div>

<!-- http://jssdk.demo.qiniu.io/ -->
<!-- http://www.plupload.com/docs -->
<?= $this->js([
    'plupload/moxie.min',
    'plupload/plupload.full.min',
    'qiniu/qiniu.min',
])?>
<script>
var uploader = Qiniu.uploader({
    // 上传模式，依次退化
    runtimes: 'html5,flash,html4',
    
    // 上传选择的点选按钮，必需
    browse_button: 'pickfiles',

    // 在初始化时 uptoken uptoken_url uptoken_func 三个参数中必须有一个被设置
    // 如果提供了多个 其优先级为 uptoken > uptoken_url > uptoken_func
    // 其中 uptoken 是直接提供上传凭证
    // uptoken_url 是提供了获取上传凭证的地址
    // 如果需要定制获取 uptoken 的过程则可以设置 uptoken_func
    // uptoken是上传凭证 由其他程序生成
    // uptoken : '<?= ($uptoken ?? null) ?>',
    
    // Ajax请求uptoken的Url，强烈建议设置（服务端提供）
    // 需要返回 JSON 格式：uptoken => xxx
    uptoken_url: '/dep/tool/uploads/uptoken?raw=true',

    // 在需要获取uptoken时，该方法会被调用
    // uptoken_func: function() {
    //     var jqxhr = $.ajax({
    //        url: '/dep/tool/uploads/uptoken',
    //        type: 'GET',
    //        dataType: 'json',
    //        async: false,
    //        headers: {
    //             'Access-Control-Allow-Origin': '*',
    //             'AUTHORIZATION': ''
    //        },
    //        success: function (res) {
    //        },
    //        error: function (xhr, status) {
    //        }
    //     });

    //     return ((typeof jqxhr.responseJSON == 'undefined')
    //         || (typeof jqxhr.responseJSON.dat.token == 'undefined')
    //     ) ? false : jqxhr.responseJSON.dat.token;
    // },

    // 设置上传文件的时候是否每次都重新获取新的 uptoken
    get_new_uptoken: false,
    
    // AJAX 请求 downToken 的 Url 私有空间时使用
    // JS-SDK 将向该地址 POST 文件的 key 和 domain
    // 服务端返回的 JSON 必须包含 url 字段 url 值为该文件的下载地址
    // downtoken_url: '/downtoken',
    
    // 若开启该选项，JS-SDK 会为每个文件自动生成 key
    // 默认 false，key 为文件名
    unique_names: true,
    
    // 若在服务端生成 uptoken 的上传策略中指定了sava_key 则开启
    // SDK 在前端将不对 key 进行任何处理 默认 false
    // save_key: true,
    
    // bucket 域名 下载资源时用到 必需
    domain: 'http://assets.hcmchi.com',
    
    // 上传区域 DOM ID，默认是 browser_button 的父元素
    container: 'qnupload-container',
    
    // 最大文件体积限制
    max_file_size: '100mb',
    
    // 引入flash，相对路径
    flash_swf_url: '/assets/plupload/Moxie.swf',
    
    // 上传失败最大重试次数
    max_retries: 3,
    
    // 开启可拖曳上传
    // dragdrop: true,
    
    // 拖曳上传区域元素的ID，拖曳文件或文件夹后可触发上传
    // drop_element: 'container',
    
    // 分块上传时，每块的体积
    chunk_size: '4mb',
    
    // 选择文件后自动上传，若关闭需要自己绑定事件触发上传
    auto_start: true,
    // x_vars : {
    //    查看自定义变量
    //    'time' : function(up,file) {
    //        var time = (new Date()).getTime();
              // do something with 'time'
    //        return time;
    //    },
    //    'size' : function(up,file) {
    //        var size = file.size;
              // do something with 'size'
    //        return size;
    //    }
    // },

    init: {
        // 文件添加进队列后，处理相关的事情
        'FilesAdded': function(up, files) {
            plupload.each(files, function(file) {
            });
        },

        // 每个文件上传前，处理相关的事情
        'BeforeUpload': function(up, file) {
        },

        // 每个文件上传时，处理相关的事情
        'UploadProgress': function(up, file) {
            $('#upload-progress-container').width('200px')
            var progress = this.total.percent + '%'
            $('#upload-progress')
            .html(progress)
            .width(progress)
            .css('padding-left', '10px')
        },

        // 每个文件上传成功后，处理相关的事情
        // 其中info是文件上传成功后，服务端返回的json，形式如：
        // {
        //    "hash": "Fh8xVqod2MQ1mocfI4S4KpRL6D98",
        //    "key": "gogopher.jpg"
        //  }
        'FileUploaded': function(up, file, info) {
            // 获取上传成功后的文件的 URL
            var domain = up.getOption('domain')
            var upres  = JSON.parse(info)

            $('#upload-res').val(domain + '/' + upres.key)
            $('input[name="filekey"]').val(upres.key)
            $('.invisible-default').show()
        },

        // 上传出错时，处理相关的事情
        'Error': function(up, err, errTip) {
            alert(errTip)
        },

        // 队列文件处理完毕后，处理相关的事情
        'UploadComplete': function() {
        },

        // 若想在前端对每个文件的key进行个性化处理，可以配置该函数
        // 该配置必须要在unique_names: false，save_key: false时才生效
        'Key': function(up, file) {
            // var key = ''
            // do something with key ...
            // return key
        }
    }
})
</script>