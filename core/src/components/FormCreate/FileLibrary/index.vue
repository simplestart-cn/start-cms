<template>
  <div>
    <div class="flex align-center">
      <div class="margin-top-xs">
        <ul class="el-upload-list el-upload-list--picture-card">
          <li class="el-upload-list__item is-success" v-for="(item, index) in srcList" :key="index" style="width: 80px; height: 80px">
            <img :src="item.url || item" alt="" class="el-upload-list__item-thumbnail" />
            <span class="el-upload-list__item-actions">
              <span class="el-upload-list__item-preview" @click.stop="handleShow(item)"><i class="el-icon-zoom-in"></i></span>
              <span class="el-upload-list__item-delete" @click.stop="handleDelete(index)" v-if="!disabled"><i class="el-icon-delete"></i></span>
            </span>
          </li>
        </ul>
      </div>
      <div v-if="srcList.length < limit" class="flex flex-direction align-center justify-center">
        <div class="upload-container">
          <el-upload
            action="#"
            :accept="accept"
            v-bind="$attrs"
            :show-file-list="false"
            :http-request="handleUpload"
            class="image-uploader"
            list-type="picture-card"
            :disabled="disabled"
          >
            <i class="el-icon-plus image-uploader-icon" />
          </el-upload>
        </div>
        <a class="text-blue" style="font-size: 12px; text-decoration: underline;" @click="handleDialog" v-if="library">文件库</a>
      </div>
    </div>
    <el-dialog :visible.sync="dialogVisible" :append-to-body="true" width="500px">
      <div class="text-center">
        <img width="450px" :src="dialogImage.url" :alt="dialogImage.name" />
      </div>
    </el-dialog>
    <library ref="fileDialog" :accept="accept" :size="size" v-bind="$attrs" :limit="limit" :disabled="!library"  @updateRow="updateRow" v-if="library" />
  </div>
</template>

<script>
import openWindow from '@/utils/open-window'
import library from './library'
import { getArrByKey } from '@/utils'
import { upload } from '@/api/storage/file'
import mixins from './mixins'
export default {
  name: 'FileLibrary',
  components: {
    library
  },
  mixins: [mixins],
  props: {
    /**
      * value: 数组或对象形式。因为双向绑定，所以可能会出现字符串形式。
      * returnIds：控制返回值是否为id拼凑的字符串，默认返回路径地址数组
      * disabled: 禁用选择，默认 false
      * limit: 限制上传数，默认 5
      * multiple: library组件中使用，是否可以多选上传
      * uploadObj: library组件中使用，上传是携带的参数，默认{ folder_id: '' }
      * accept: 配置上传所选的类型，默认'image/*'
      * size: 配置上传所选的大小，默认5MB
      * library: 是否使用图片库
    * */
    value: [Object, Array, String], // 外部 Object, Array 传入的value值，用于图片操作。 String 为内部调整
    returnIds: { // 控制返回值是否为id拼凑的字符串
      type: [Boolean, Number],
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    limit: {
      type: [Number, String],
      default: 5
    },
    accept: {
      type: String,
      default: 'image/*,video/*,.dwg,.dxf,.psd,.ai,.pdf,.txt,.md,.doc,.docx,.xls,.xlsx,.pptx'
    },
    size: {
      type: Number,
      default: 5
    },
    library: {
      type: [Boolean,Number],
      default: true
    }
  },
  data() {
    return {
      srcList: [],
      dialogVisible: false,
      dialogImage: {},
      imgLoading: false
    }
  },
  watch: {
    value: {
      handler(val) {
        // 转化字符串后，会再次进入，需要将字符串判定退出
        if (typeof val === 'string') {
          if (val.indexOf('http') > -1) {
            this.srcList = [val]
          }
          return
        }
        if (val && typeof val === 'object') {
          if (!Array.isArray(val)) {
            val = [val]
          }
          this.srcList = val
        } else {
          this.srcList = []
        }
      },
      immediate: true,
      deep: true
    },
    srcList: {
      handler(val) {
        this.handleEmit(val)
      },
      deep: true,
      immediate: true,
    }
  },
  methods: {
    // 查看大图
    handleShow(image) {
      openWindow(image.url, '预览', '500', '400')
    },
    // 删除图片某个图片列表
    handleDelete(index) {
      if (!this.disabled) {
        this.$confirm('是否要删除所选图片', '提示', {
          center: true
        }).then(() => {
          this.srcList.splice(index, 1)
          this.$message.success('删除成功')
        }).catch(() => {
          this.$message.warning('您取消了删除')
        })
      }
    },
    handleDialog() {
      if (!this.disabled) {
        this.$refs.fileDialog.handleShow()
      }
    },
    // 将数组转为字符串
    handleEmit(list) {
      if (this.returnIds) {
        const ids = getArrByKey(list, 'id').join(',')
        this.$emit('input', ids)
      } else {
        if (this.limit === 1) {
          this.$emit('input', list.map(item => item.url || item).join(','))
        } else {
          this.$emit('input', list.map(item => item.url || item))
        }
      }
    },
    // 手动上传
    handleUpload(data) {
      const formData = this.createFormData(data, this.upLoadData, this.accept, this.size)
      if (!formData) {
        return false
      }
      this.imgLoading = true
      upload(formData).then(response => {
        this.updateRow([response.data])
        this.imgLoading = false
      }).catch(error => {
        this.imgLoading = false
      })
    },
    updateRow(list) {
      const srcList = this.srcList.concat(list)
      if (srcList.length > this.limit) {
        srcList.splice(this.limit)
      }
      this.srcList = [].concat(srcList)
    }
  }
}
</script>

<style lang="scss" scoped>
.image-uploader {
  :deep().el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    line-height: 65px;
    width: 60px;
    height: 60px;
    .el-upload-dragger{
      height: 100%;
    }
    &:hover{
     border-color: #409EFF;
   }
  }
  .image-uploader-icon {
    font-size: 20px;
    color: #8c939d;
    width: 60px;
    height: 60px;
    line-height: 60px;
    text-align: center;
  }
}
</style>
