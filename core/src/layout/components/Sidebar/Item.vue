<script>
export default {
  name: 'Item',
  functional: true,
  props: {
    icon: {
      type: String,
      default: ''
    },
    title: {
      type: String,
      default: ''
    },
    collapse: {
      type: Boolean,
      default: false
    },
  },
  render(h, context) {
    const { icon, title, collapse } = context.props
    const vnodes = []

    if (icon) {
      vnodes.push(<i class={icon}/>)
    }

    // if (title) {
    //   vnodes.push(<span slot='title'>{(title.substring(0,2))}</span>)
    // }

    if (title) {
      if(collapse){
        vnodes.push(<span>&nbsp;&nbsp;{title.substring(0,2)}</span>)
      }else{
        vnodes.push(<span slot='title'>{title}</span>)
      }
      
    }
    return vnodes
  }
}
</script>
