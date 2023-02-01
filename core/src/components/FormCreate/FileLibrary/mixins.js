export default {
  methods: {
    validateUpload(file, ...props) {
      const [ accept, size ] = props
      // 判断文件类型
      const ext = file.name.substring(file.name.lastIndexOf('.')+1)
      const isImg =  file.type.split('\/')[0] === 'image'
      const isVideo =  file.type.split('\/')[0] === 'image'
      const isAccept = accept.split(',').indexOf('.'+ext) >= 0
      const isLt5M = file.size / 1024 / 1024 < (size || 5)
      const isLt10M = file.size / 1024 / 1024 < (size || 10)
      if (!isImg && !isVideo && !isAccept) {
        this.$message.error(`文件格式不允许!`)
        return false
      }
      if (isImg && !isLt5M) {
        this.$message.error(`上传图片大小不能超过 ${ size }MB!`)
        return false
      }
      if (isVideo && !isLt10M) {
        this.$message.error(`上传视频大小不能超过 ${ size }MB!`)
        return false
      }
      return true
    },
    /**
     * data 文件上传的数据
     * uploadData 需要上传的参数对象
     * */
    createFormData(data, upLoadData, ...props) {
      // 上传
      const file = data.file
      if (this.validateUpload(file, ...props)) {
        const formData = new FormData()
        formData.append('file', file)
        for (let key in upLoadData) {
          // 其他参数
          formData.append(key, upLoadData[key])
        }
        return formData
      }
      return false
    }
  }
}
