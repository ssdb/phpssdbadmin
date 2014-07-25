/**
 * @version 1.1
 * @author ideawu@163.com
 * @link http://www.ideawu.net/
 * @class
 * 分页控件, 使用原生的JavaScript代码编写. 重写onclick方法, 获取翻页事件,
 * 可用来向服务器端发起AJAX请求.
 *
 * @param {String} id: HTML节点的id属性值, 控件将显示在该节点中.
 * @returns {PagerView}: 返回分页控件实例.
 *
 * @example
 * ### HTML:
 * &lt;div id="pager"&gt;&lt;/div&gt;
 *
 * ### JavaScript:
 * var pager = new PagerView('pager');
 * pager.index = 3; // 当前是第3页
 * pager.size = 16; // 每页显示16条记录
 * pager.itemCount = 100; // 一共有100条记录
 *
 * pager.onclick = function(index){
 *	alert('click on page: ' + index);
 *	// display data...
 * };
 *
 * pager.render();
 * 
 */
var PagerView = function(id){
	var self = this;
	this.id = id;

	this._pageCount = 0; // 总页数
	this._start = 1; // 起始页码
	this._end = 1; // 结束页码

	/**
	 * 当前控件所处的HTML节点引用.
	 * @type DOMElement
	 */
	this.container = null;
	/**
	 * 当前页码, 从1开始
	 * @type int
	 */
	this.index = 1;
	/**
	 * 每页显示记录数
	 * @type int
	 */
	this.size = 15;
	/**
	 * 显示的分页按钮数量
	 * @type int
	 */
	this.maxButtons = 9;
	/**
	 * 记录总数
	 * @type int
	 */
	this.itemCount = 0;

	/**
	 * 控件使用者重写本方法, 获取翻页事件, 可用来向服务器端发起AJAX请求.
	 * 如果要取消本次翻页事件, 重写回调函数返回 false.
	 * @param {int} index: 被点击的页码.
	 * @returns {Boolean} 返回false表示取消本次翻页事件.
	 * @event
	 */
	this.onclick = function(index){
		return true;
	};

	/**
	 * 内部方法.
	 */
	this._onclick = function(index){
		var old = self.index;

		self.index = index;
		if(self.onclick(index) !== false){
			self.render();
		}else{
			self.index = old;
		}
	};

	/**
	 * 在显示之前计算各种页码变量的值.
	 */
	this._calculate = function(){
		self._pageCount = parseInt(Math.ceil(self.itemCount / self.size));
		self.index = parseInt(self.index);
		if(self.index > self._pageCount){
			self.index = self._pageCount;
		}
		if(self.index < 1){
			self.index = 1;
		}

		self._start = Math.max(1, self.index - parseInt(self.maxButtons/2));
		self._end = Math.min(self._pageCount, self._start + self.maxButtons - 1);
		self._start = Math.max(1, self._end - self.maxButtons + 1);
	};

	/**
	 * 获取作为参数的数组落在相应页的数据片段.
	 * @param {Array[Object]} rows
	 * @returns {Array[Object]}
	 */
	this.page = function(rows){
		self._calculate();

		var s_num = (self.index - 1) * self.size ;
		var e_num = self.index * self.size;

		return rows.slice(s_num, e_num);	
	};

	/**
	 * 渲染控件.
	 */
	this.render = function(){
		var div = document.getElementById(self.id);
		div.view = self;
		self.container = div;

		self._calculate();

		var str = '';
		str += '<div class="PagerView">\n';
		if(self._pageCount > 1){
			if(self.index != 1){
				str += '<a href="javascript://1"><span>|&lt;</span></a>';
				str += '<a href="javascript://' + (self.index-1) + '"><span>&lt;&lt;</span></a>';
			}else{
				str += '<span>|&lt;</span>';
				str += '<span>&lt;&lt;</span>';
			}
		}
		for(var i=self._start; i<=self._end; i++){
			if(i == this.index){
				str += '<span class="on">' + i + "</span>";
			}else{
				str += '<a href="javascript://' + i + '"><span>' + i + '</span></a>';
			}
		}
		if(self._pageCount > 1){
			if(self.index != self._pageCount){
				str += '<a href="javascript://' + (self.index+1) + '"><span>&gt;&gt;</span></a>';
				str += '<a href="javascript://' + self._pageCount + '"><span>&gt;|</span></a>';
			}else{
				str += '<span>&gt;&gt;</span>';
				str += '<span>&gt;|</span>';
			}
		}
		str += ' 一共' + self._pageCount + '页, ' + self.itemCount + '条记录 ';
		str += '</div><!-- /.pagerView -->\n';

		self.container.innerHTML = str;

		var a_list = self.container.getElementsByTagName('a');
		for(var i=0; i<a_list.length; i++){
			a_list[i].onclick = function(){
				var index = this.getAttribute('href');
				if(index != undefined && index != ''){
					index = parseInt(index.replace('javascript://', ''));
					self._onclick(index)
				}
				return false;
			};
		}
	};

}
