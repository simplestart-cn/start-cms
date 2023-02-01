<template>
  <div>
	  <div ref="editor" class="text">
			<p v-html="value"></p>
		</div>
		<file-library ref="fileLibrary" :upload-obj="{ type: 'image' }" @updateRow="updateRow" />
	</div>
</template>

<script>
import WangEditor from 'wangeditor'
import fileLibrary from '../FileLibrary/library'
export default {
	name: 'WangEditor',
	components: {
		fileLibrary
	},
	props: {
		value: {
			type: String,
			default: ''
		},
		disabled: {
			type: Boolean,
			default: false
		},
		placeholder: {
			type: String,
			default: ''
		},
		height: {
			type: Number,
			default: 500
		}
	},
	data() {
		return {
			editor: null,
			status: false
		}
	},
	watch: {
		value(val) {
			if(this.editor) {
				this.editor.txt.html(val)
			}
		},
		disabled(val) {
			if(this.editor) {
				if(val) {
					this.editor.disable()
				} else {
					this.editor.enable()
				}
			}
		}
	},
	mounted() {
		this.setEditor()
	},
	methods: {
		setEditor() {
			const wangEditor = new WangEditor(this.$refs.editor)
			this.selfMenu(wangEditor)
			this.setFunc(wangEditor)
			this.setConfig(wangEditor)
			this.setMenu(wangEditor)
			wangEditor.create()
			this.editor = wangEditor
		},
		setFunc(editor) {
			// 配置回调函数
			editor.config.onblur = () => {
				this.status = true
			}
			editor.config.onfocus = newHtml => {
				if(this.status) {
					this.$emit('input', newHtml)
					this.status = false
				}
			}
			editor.config.onchange = newHtml => {
				this.$emit('input', newHtml)
			}
		},
		setConfig(editor) {
			editor.config.placeholder = this.placeholder || ''
			editor.config.onchangeTimeout = 2000 // 2秒钟未改变自动保存
			editor.config.height  = this.height // 设置高度
		},
		setMenu(editor) {
			editor.config.menus = [
				'head',
				'bold',
				'fontSize',
				'fontName',
				'italic',
				'underline',
				'strikeThrough',
				'indent',
				'foreColor',
				'backColor',
				'link',
				'LabraryMenu',
				'list',
				'justify',
				'table',
				'splitLine',
			]
		},
		// 局部创建新的自定义按钮
		selfMenu(editor) {
			const that = this
			const { $, BtnMenu } = WangEditor
			class LabraryMenu extends BtnMenu {
				constructor(editor) {
					const $elem = $(
						`<div class="w-e-menu picture" data-title="图片库">
							<i class="el-icon-picture" />
						</div>`
					)
					super($elem, editor)
				}
				clickHandler() {
					that.$refs.fileLibrary.handleShow()
				}
				// 这个必须有，不然会报错
				tryChangeActive() {}
			}
			// 注册
			editor.menus.extend('LabraryMenu', LabraryMenu)
		},
		updateRow(images) {
			images.forEach(item => {
				if(item.type === 'video') {
					this.editor.txt.append(`<video src="${ item.url }" style="max-width: 100%;" contenteditable="${ false }" />`)
					return 
				}
				this.editor.txt.append(`<img src="${ item.url }" style="max-width: 100%;" contenteditable="${ false }" />`)
			})
		}
	}
}
</script>

<style lang="scss" scoped>
.toolbar {
	border: 1px solid #ccc;
}
.text {
	border: 1px solid #ccc;
	min-height: 400px;
}
</style>