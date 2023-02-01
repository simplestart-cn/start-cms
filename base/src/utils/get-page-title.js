import defaultSetting from '@/setting'

const title = defaultSetting.title || '简艺管理系统'

export default function getPageTitle(pageTitle) {
  if (pageTitle) {
    return `${pageTitle} - ${title}`
  }
  return `${title}`
}
