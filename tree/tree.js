var Tree = new function() {
    this._url = "tree.php";             //用于请求数据的服务器页面地址

    this._openMark = "-";              //目录节点处于展开状态时的标识
    this._closeMark = "＋";             //目录节点处于关闭状态时的标识
    this._itemMark = "·";              //非目录节点标识

    this._initId = "treeInit";          //树形目录初始div标识
    this._rootData = "$";          //根节点文字信息
    this._boxSuffix = "_childrenBox";   //子节点容器后缀

    this._folderType = "folder";        //目录节点类型变量
    this._itemType = "item";            //非目录节点类型变量

    //初始化根节点
    this.init = function() {
        var initNode = document.getElementById(this._initId);   //获取初始div

        var _node = document.createElement("div");              //创建新div作为根节点
        _node.id = "0";                                         //根节点id为0
        _node.innerHTML = this.createItemHTML(_node.id, this._folderType, this._rootData);

        initNode.appendChild(_node);                            //将根节点加入初始div
    }

    //获取给定节点的子节点
    this.getChildren = function(_parentId, dir) {
        //获取页面子节点容器box
        var childBox = document.getElementById(_parentId + this._boxSuffix);

        //如果子节点容器已存在则直接设置显示状态，否则从服务器获取子节点信息
        if (childBox) {
            var isHidden = (childBox.style.display == "none");      //判断当前状态是否隐藏
            childBox.style.display = isHidden?"":"none";            //隐藏则显示，如果显示则变为隐藏

            //根据子节点的显示状态修改父节点标识
            var _parentNode = document.getElementById(_parentId);
            _parentNode.firstChild.innerHTML = isHidden?this._openMark:this._closeMark;
        } else {
            var xmlHttp=this.createXmlHttp();                       //创建XmlHttpRequest对象
            xmlHttp.onreadystatechange = function() {
                if (xmlHttp.readyState == 4) {
                    //调用addChildren函数生成子节点 
                    Tree.addChildren(_parentId, xmlHttp.responseXML);
                }
            }
            xmlHttp.open("GET",this._url+".?parentId=" + _parentId + '&d='+dir, true);
            xmlHttp.send(null);
        }
    }

    //根据获取的xmlTree信息，设置指定节点的子节点
    this.addChildren = function(_parentId, _data) {
        var _parentNode = document.getElementById(_parentId);   //获取父节点
        _parentNode.firstChild.innerHTML = this._openMark;      //设置节点前标记为目录展开形式

        var _nodeBox = document.createElement("div");           //创建一个容器，称为box，用于存放所有子节点
        _nodeBox.id = _parentId + this._boxSuffix;              //容器的id规则为：在父节点id后加固定后缀
        _nodeBox.className = "box";                             //样式名称为box，div.box样式会对此节点生效
        _parentNode.appendChild(_nodeBox);                      //将子节点box放入父节点中

        var _children = _data.getElementsByTagName("tree")[0].childNodes;   //获取所有item节点
        var _child = null;                                      //声明_child变量用于保存每个子节点
        var _childType = null;                                  //声明_childType变量用于保存每个子节点类型
	var _dir=null;

        for(var i=0; i<_children.length; i++) {                 //循环创建每个子节点
            _child = _children[i];
            _node = document.createElement("div");              //每个节点对应一个新div             
            _node.id = _child.getAttribute("id");               //节点的id值就是获取数据中的id属性值
            _childType = _child.getAttribute("isFolder")=="true"?this._folderType:this._itemType;   //设置子节点类型
	_dir=_child.getAttribute("dir");
            //根据节点类型不同，调用createItemHTML创建节点内容
            if (_childType == this._itemType) {
                //非目录节点在最后多传一个link数据，用于点击后链接到新页面
                _node.innerHTML = this.createItemHTML(_node.id, _childType, _child.firstChild.data, _child.getAttribute("link"),_dir);
            } else {
                //目录节点只需传递id，节点类型，节点数据
                _node.innerHTML = this.createItemHTML(_node.id, _childType, _child.firstChild.data, _child.getAttribute("link"),_dir);
            }

            _nodeBox.appendChild(_node);                        //将创建好的节点加入子节点box中
        }
    }

    //创建节点的页面片断
    this.createItemHTML = function(itemId, itemType, itemData, itemLink,itemdir) {
        //根据节点类型不同，返回不同的HTML片断
        if (itemType == this._itemType) {
            //非目录节点的class属性以item开头，并且onclick事件调用Tree.clickItem函数
            return '<span class="itemMark">'+this._itemMark+'</span>' +
                   '<span class="item" onclick="Tree.clickItem(\'' + itemLink  + '\');">'+itemData+'</span>';
        } else if (itemType == this._folderType) {
            //目录节点的class属性以folder开头，并且onclick事件调用Tree.getChildren函数
            return '<span class="folderMark" onclick="Tree.getChildren(' + itemId +', ' + itemdir + ')">' + this._closeMark + '</span>' +
                   '<span class="folder" onclick="Tree.clickItem(\'' + itemLink  + '\')">' + itemData + '</span>'
        }
    }

    //点击叶子节点后的动作，目前只是弹出对话框，可修改为链接到具体的页面
    this.clickItem = function(_link) {
        alert("当前节点可以链接到页面 " + _link + " 。");
    }

    //用于创建XMLHttpRequest对象
    this.createXmlHttp=function() {
        var xmlHttp = null;
        //根据window.XMLHttpRequest对象是否存在使用不同的创建方式
        if (window.XMLHttpRequest) {
           xmlHttp = new XMLHttpRequest();                  //FireFox、Opera等浏览器支持的创建方式
        } else {
           xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");//IE浏览器支持的创建方式
        }
        return xmlHttp;
    }
}
