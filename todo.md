思路：
从http://www.zimuzu.tv/eresourcelist开始爬
如果url不是/resource/*就跳过匹配/resource/list/*的url存入缓存
没有链接可爬就回到/eresource

一个进程用来抓url，另一个进程用来抓下载链接
