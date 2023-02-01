<template>
  <el-dialog title="文件库" :visible.sync="visible" center width="850px" :append-to-body="true">
    <el-row>
      <el-col :span="4">
        <el-menu
          :default-active="tabsValue"
          class="menu-demo"
          @select="handleChange">
          <el-menu-item v-for="item in editableTabs" :key="item.id" :index="item.name" class="">
            <i v-if="Number(item.id) > 0 && tabsValue == item.id" class="el-icon-edit fl margin-top" @click.stop="handleEdit(item)"></i>
            <span slot="title" :title="item.title" class="one-hidden">{{ item.title }}</span>
            <i v-if="Number(item.id) > 0 && tabsValue == item.id" class="el-icon-close fr margin-top" @click.stop="handleRemoveGroup(item)"></i>
          </el-menu-item>
        </el-menu>
        <el-row type="flex" justify="center" class="margin-top-sm">
          <el-button size="mini" type="success" icon="el-icon-plus" @click="handleAdd">添加分组</el-button>
        </el-row>
      </el-col>
      <el-col :span="20">
        <el-row class="margin-bottom-sm margin-left-xs">
          <el-col :span="20">
            <el-dropdown @command="handleTransfer" trigger="click">
              <el-button size="mini" type="primary">移动至分组<i class="el-icon-arrow-down el-icon--right" /></el-button>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item v-for="item in editableTabs.filter(item => item.id > 0)" :key="item.id" :command="item.name" :disabled="item.name === tabsValue">{{ item.title }}</el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
            <el-button size="mini" type="danger" icon="el-icon-delete" class="margin-left-sm" @click="handleCancel">删除</el-button>
          </el-col>
          <el-col :span="2">
            <el-upload
              ref="imgUpload"
              action="#"
              :accept="accept"
              :multiple="multiple"
              :show-file-list="false"
              :http-request="handleUpload"
            >
              <el-button size="mini" type="success" icon="el-icon-upload">上传图片</el-button>
            </el-upload>
          </el-col>
        </el-row>
        <el-row :gutter="10" v-loading="imgLoading" class="image-content">
          <el-col :span="6" v-for="(item, index) in imageData.data" :key="item.id">
            <div class="image-item padding-tb margin-lr-sm margin-bottom-sm flex flex-direction justify-center align-center" @click="handleCheck(index, item)">
              <img :src="item.url" :alt="item.name" :title="item.name" v-if="item.type === 'image'">
              <video height="90" :src="item.url" :alt="item.name" :title="item.name" controls v-else-if="item.type === 'video'">您的浏览器不支持 video 标签。</video>
              <div class="document" v-else><i class="el-icon-document" /></div>
              <div v-if="item.status" class="image-cover">
                <i class="el-icon-check text-green" />
              </div>
              <span :title="item.name" class="margin-top one-hidden">{{ item.name }}</span>
            </div>
          </el-col>
        </el-row>
        <el-row type="flex" justify="center">
          <el-pagination :current-page="imageData.page" :page-size="imageFilter.per_page" :total="imageData.total" background layout="total, prev, pager, next, jumper" @current-change="handleCurrentChange" />
        </el-row>
      </el-col>
    </el-row>
    <el-row type="flex" justify="center" class="margin-top">
      <el-button size="small" type="primary" @click="updateRow">确认</el-button>
      <el-button size="small" type="danger" @click="handleClose">取消</el-button>
    </el-row>
    <group-dialog ref="groupDialog" @updateTabs="updateTabs" />
  </el-dialog>
</template>

<script>
import { getPage, transfer, remove, upload } from '@/api/storage/file'
import { getList as getFolder, remove as removeFolder } from '@/api/storage/folder'
import { getArrByKey } from '@/utils'
import groupDialog from './group'
import mixins from './mixins'

export default {
  name: 'Library',
  components: {
    groupDialog
  },
  mixins: [mixins],
  props: {
    limit: {
      type: [Number, String],
      default: Infinity
    },
    multiple: {
      type: Boolean,
      default: true
    },
    uploadObj: {
      type: Object,
      default: () => {
        return {
          folder_id: 0
        }
      }
    },
    accept: {
      type: String,
      default: 'image/*'
    },
    size: {
      type: Number,
      default: 5
    },
    disabled: {
      type: [Boolean,Number],
      default: false
    }
  },
  data() {
    return {
      visible: false,
      tabsValue: '-1',
      imgLoading: false,
      editableTabs: [{
        id: -1,
        title: '全部',
        name: '-1'
      }, {
        id: 0,
        title: '未分组',
        name: '0'
      }],
      imageData: {
        data: [],
        total: 0
      },
      imageCheck: [],
      imageFilter: {
        page: 1,
        per_page: 30
      }
    }
  },
  computed: {
    upLoadData() {
      return JSON.parse(JSON.stringify(this.uploadObj))
    }
  },
  mounted() {
    this.tabsMounted()
  },
  methods: {
    initData() {
      this.imageFilter.folder_id = this.tabsValue
      if (this.tabsValue == -1) {
        delete this.imageFilter.folder_id
      }
      this.imageFilter.page = 1
      this.fetchList()
      this.imageFilter = {
        page: 1,
        per_page: 30
      }
      this.imageCheck = []
    },
    fetchList() {
      this.imgLoading = true
      getPage(this.imageFilter).then(response => {
        const imageData = response.data
        imageData.data.forEach(item => { item.status = false })
        this.imageData = imageData
        this.imgLoading = false
      }).catch(() => {
        this.imgLoading = true
      })
    },
    // 跳转页
    handleCurrentChange(val) {
      this.imageFilter.page = val
      this.fetchList()
    },
    // 展示图片库
    handleShow() {
      this.visible = true
      this.initData()
    },
    // 关闭图片库
    handleClose() {
      this.visible = false
    },
    // 选择图片
    handleCheck(index, img) {
      const status = this.imageData.data[index].status
      if (!status) {
        if (this.imageCheck.length >= this.limit) {
          this.$message.warning('该内容只能插入' + this.limit + '张图片')
          return false
        }
        if (!this.imageCheck.some(item => img.id === item.id)) {
          this.imageCheck.push(img)
        }
      } else {
        this.imageCheck = this.imageCheck.filter(item => item.id !== img.id)
      }
      this.imageData.data[index].status = !status
    },
    // 添加分组
    handleAdd() {
      this.$refs.groupDialog.handleCreate()
    },
    // 删除图片
    handleCancel() {
      let ids = getArrByKey(this.imageCheck, 'id')
      if (ids.length < 2) {
        ids = ids.toString()
      } else {
        ids = ids.join(',')
      }
      this.$confirm('是否删除所选图片', '提示', {}).then(() => {
        remove({ id: ids }).then(response => {
          if (response.code == 0) {
            this.initData()
            this.$message.success(response.msg)
          } else {
            this.$message.error(response.msg)
          }
        }).catch(error => {
          this.$message.error(error.message)
        })
      }).catch(() => {
        this.$message.error('您取消了删除')
      })
    },
    // 删除分组
    handleRemoveGroup(data) {
      if (data.id > 0) {
        this.$confirm('是否删除分组', '提示', {}).then(() => {
          removeFolder({ id: data.id }).then(response => {
            if (response.code == 0) {
              const tabs = this.editableTabs
              let activeName = this.tabsValue
              if (activeName == data.name) {
                tabs.forEach((tab, index) => {
                  if (tab.name == data.name) {
                    const nextTab = tabs[index + 1] || tabs[index - 1]
                    if (nextTab) {
                      activeName = nextTab.name
                    }
                  }
                })
              }
              this.tabsValue = activeName
              this.editableTabs = tabs.filter(tab => tab.name != data.name)
              this.$message.success(response.msg)
              this.initData()
            } else {
              this.$message.error(response.msg)
            }
          }).catch(error => {
            this.$message.error(error.message)
          })
        }).catch(() => {
          this.$message.warning('您取消了删除')
        })
      }
    },
    // 移动分组
    handleTransfer(groupId) {
      const ids = getArrByKey(this.imageCheck, 'id')
      if (ids.length > 0) {
        this.imgLoading = true
        transfer({ id: ids, folder_id: groupId }).then(response => {
          if (response.code == 0) {
            this.initData()
            this.$message.success(response.msg)
          } else {
            this.$message.error(response.msg)
          }
          this.imgLoading = false
        }).catch(error => {
          this.$message.error(error.message)
          this.imgLoading = false
        })
      } else {
        this.$message.error('请选择所需要移动的图片')
      }
    },
    // 编辑分组
    handleEdit(row) {
      this.$refs.groupDialog.handleEdit(row)
    },
    // 手动上传
    handleUpload(data) {
      const formData = this.createFormData(data, this.upLoadData, this.accept, this.size)
      if (!formData) {
        return false
      }
      this.imgLoading = true
      upload(formData).then(response => {
        this.initData()
        this.imgLoading = false
      }).catch(error => {
        this.initData()
        console.log(error)
        this.imgLoading = false
      })
    },
    // 切换分组
    handleChange(id) {
      // 获取数据
      this.tabsValue = id
      this.upLoadData.folder_id = id
      this.initData()
      return true
    },
    // 添加/编辑分组
    updateTabs(form) {
      const index = this.editableTabs.some(item => {
        if (item.id == form.id) {
          item.title = form.title
          return true
        } else {
          return false
        }
      })
      if (!index) {
        this.editableTabs.push(form)
      }
      this.tabsValue = form.name
    },
    // 确认选择的图片
    updateRow() {
      this.imageCheck.sort((a, b) => a.id - b.id)
      this.$emit('updateRow', this.imageCheck)
      this.handleClose()
    },
    tabsMounted() {
      getFolder().then(response => {
        this.editableTabs = this.editableTabs.concat(response)
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.menu-demo {
  height: 400px;
  overflow: auto;
  > .el-menu-item {
    width: 130px;
    text-align: center;
    font-size: 14px;
    padding: 0 10px !important;
    height: 48px;
    line-height: 48px;
    > span {
      width: 55%;
      height: 16px;
      line-height: 16px;
    }
    > i {
      margin-right: 0px;
    }
  }
}

.el-icon-edit:hover,
.el-icon-close:hover {
  color: red
}

.image-content {
  padding: 0;
  width: 100%;
  height: 350px;
  align-content: flex-start;
  overflow: auto;
}
.image-item:hover {
  border: 1px solid rgb(53, 189, 232);
}
.image-item {
  border-radius: 10px;
  border: 1px solid rgb(232, 232, 232);
  overflow: hidden;
  position: relative;
  cursor: pointer;
  > .document {
    height: 90px;
    line-height: 90px;
    text-align: center;
    font-size: 52px; 
  }
  > .image-cover {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    background: rgba(40, 40, 40, 0.27);
    z-index: 10;
    > i {
      width: 100%;
      height: 100%;
      font-size: 135px;
    }
  }
  > span {
    width: 85%;
    height: 16px;
    line-height: 16px;
    text-align: center;
  }
  > img {
    width: 85%;
    height: 90px;
  }
}
.one-hidden {
  overflow: hidden;
  text-overflow: ellipsis;
  display: inline-block;
  white-space: nowrap;
}
</style>
