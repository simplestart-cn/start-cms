<template>
    <div class="upload-container">
        <el-dialog width="40%" center modal :visible.sync="dialogVisible">
            <div class="padding" v-if="uploadSuccess">
                <el-steps :active="result.step">
                    <el-step title="上传" icon="el-icon-upload">
                        <div slot="description">
                            <div>应用名：{{result.appInfo.title}}</div>
                            <div>开发者：{{result.appInfo.author}}</div>
                            <div>版本号：{{result.appInfo.version}}</div>
                        </div>
                    </el-step>
                    <el-step title="覆盖" icon="el-icon-refresh" v-if="result.cover">
                        <div slot="description">
                            <div>原应用名：{{result.coverInfo.title}}</div>
                            <div>原开发者：{{result.coverInfo.author}}</div>
                            <div>原版本号：{{result.coverInfo.version}}</div>
                        </div>
                    </el-step>
                    <el-step title="安装" icon="el-icon-finished">
                        <div slot="description" v-if="result.step == 3">
                            <div>应用名：{{result.appInfo.title}}</div>
                            <div>开发者：{{result.appInfo.author}}</div>
                            <div>版本号：{{result.appInfo.version}}</div>
                        </div>
                    </el-step>
                </el-steps>
                <div class="padding text-center text-content" v-if="result.message">{{result.message}}</div>
                <div class="flex align-center justify-center margin-top" v-if="result.step == 2">
                    <el-button class="margin-right" @click="handleCover(false)">取消</el-button>
                    <el-button type="primary" @click="handleCover(true)">确认</el-button>
                </div>
                <div class="flex align-center justify-center margin-top" v-if="result.step == 3">
                    <el-button class="margin-right" @click="handleInstall(false)">取消</el-button>
                    <el-button type="primary" @click="handleInstall(true)">确认</el-button>
                </div>
            </div>
            <el-upload :data="form" :action="action" :headers="headers" :multiple="multiple" :name="name" :accept="accept" :drag="drag" :limit="limit" :file-list="fileList" :on-exceed="handleExceed" :before-upload="beforeUpload" :on-progress="onProgress" :on-success="handleSuccess" :on-error="handleError" :on-remove="handleRemove" v-else>
                <i class="el-icon-upload"></i>
                <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
                <div class="el-upload__tip" slot="tip">
                    只能上传zip文件，50M以内<br /><br />
                    安装包根目录必须包含一个应用描述文件app.json<br /><br />
                    请尽可能缩小应用文件体积,如包内含有node_module请先删除
                </div>
            </el-upload>
        </el-dialog>
    </div>
</template>

<script>
import { getArrByKey } from '@/utils'
import request from '@/utils/request'
export default {
    name: 'Uploader',
    props: {
        value: {
            type: [Array, String],
            default: () => []
        },
        config: {
            type: Object,
            default: () => {
                return {
                    name: 'file',
                    limit: 1,
                    accept: '.zip',
                    action: '/core/app/upload',
                    drag: true,
                    multiple: false,
                }
            }
        },
        header: {
            type: Object,
            default: () => {
                return {
                    'x-access-appid': '',
                    'x-access-token': ''
                }
            }
        }
    },
    data() {
        return {
            name: this.config.name,
            limit: this.config.limit,
            multiple: this.config.multiple,
            accept: this.config.accept,
            action: this.config.action,
            headers: this.header,
            drag: this.config.drag,
            form: {
                cover: false,
                action: 'upload',
                filename: this.config.name
            },
            dialogVisible: false,
            uploadSuccess: false,
            result: {},
        }
    },
    computed: {
        fileList() {
            const imgarr = []
            if (this.value === '') {
                return []
            }
            if (typeof (this.value) === 'string') {
                // eslint-disable-next-line vue/no-side-effects-in-computed-properties
                this.value = this.value.split(',')
            }
            for (let i = 0; i < this.value.length; i++) {
                imgarr.push({ url: this.value[i] })
            }
            return imgarr
        }

    },
    methods: {
        show() {
            this.dialogVisible = true;
            this.uploadSuccess = false
        },
        hidden() {
            this.dialogVisible = false
        },
        emitInput(val) {
            this.$emit('input', val)
        },
        handleRemove(file, fileList) {
            if (fileList.length > 0) {
                this.emitInput(getArrByKey(fileList, 'url'))
            } else {
                this.emitInput([])
            }
        },
        handleExceed(files, fileList) {
            this.$message.error('最多上传' + this.limit + '个文件')
        },
        beforeUpload(file) {
            const isZip = file.type === 'application/x-zip-compressed' || file.type == 'application/zip'
            const isLt5M = file.size / 1024 / 1024 < 50
            if (!isZip) {
                this.$message.error('上传文件格式只能是zip格式')
                return false
            }
            if (!isLt5M) {
                this.$message.error('上传文件大小不能超过50MB')
                return false
            }
            return isZip && isLt5M
        },
        onProgress(event, file, fileList) {
            // console.log('========onProgress=========')
            // console.log(event, file, fileList)
            // console.log('========onProgress=========')
        },
        handleSuccess(res, file, fileList) {
            console.log(res)
            if (res.code === 0) {
                this.result = res.data
                this.uploadSuccess = true;
                for (let i = 0; i < fileList.length; i++) {
                    if (fileList[i]['uid'] === file['uid']) {
                        fileList[i]['url'] = res.data.url
                        break
                    }
                }
                this.emitInput(getArrByKey(fileList, 'url'))
            } else {
                for (let i = 0; i < fileList.length; i++) {
                    if (fileList[i]['uid'] === file['uid']) {
                        fileList.splice(i, 1)
                        break
                    }
                }
                this.$message.error(res.msg)
            }
        },
        handleError(err, file, fileList) {

        },
        handleCover(confirm) {
            let result = this.result
            request({
                url: '/core/app/upload',
                method: 'post',
                data: {
                    filename: result.appInfo.path,
                    action: 'cover',
                    cover: confirm
                }
            }).then(res => {
                if(confirm){
                    if(res.code){
                        this.$message.error(res.msg)
                    }else{
                        this.result = res.data
                    }
                }else{
                    this.dialogVisible = false
                }
                
            })
        },
        handleInstall(confirm) {
            let result = this.result
            if (confirm) {
                request({
                    url: '/core/app/install',
                    method: 'post',
                    data: {
                        name: result.appInfo.name
                    }
                }).then(res => {
                    if(res.code === 0){
                        this.dialogVisible = false,
                        this.$emit('success', true);
                    }else{
                        this.$message.error(res.msg)
                    }
                })
            }else{
                this.dialogVisible = false
                this.$emit('success', false);
            }
        }

    }
}
</script>
<style rel="stylesheet/scss" lang="scss">
</style>
