String.prototype.trim = function(){
	return this.replace(/^\s+|\s+$/g,'');
};
String.prototype.isEmpty = function(){
	return this=='' || this==undefined ;
};
String.prototype.isNumber = function(){
	return /^\d+$/.test(this);
}

//数组中的位置
Array.prototype.index = function(value){
	for (var i = 0, len = this.length; i < len; i++) {
		if(this[i] == value){
			return i;
		}
	}
	return false;
}

//删除数组中某个位置
Array.prototype.remove = function(index){
	var len = this.length;
	if(index < 0 || index >= len){
		return false;
	}
	for(var i = index; i < len; i++){
		this[index] = this[++index];
	}
	this.length--;
	return true;
}

//删除数组中某个值
Array.prototype.del = function(value){
    for(var i = 1, len = this.length; i < len; i++){
        if(this[i] == value){
            this.remove(i);
            return this;
        }
    }
    return this;
}

//是否为数组
Array.prototype.isArray = function(){
    return (this instanceof Array);
}

//数组是否为空
Array.prototype.isEmpty = function(){
    return this.length == 0;
}

//差集
Array.prototype.diff = function(arr){
    if(!arr.isArray()){
        return ;
    }
    for(var i = 0, len = arr.length; i < len; i++){
        var index = this.index(arr[i]);
        if(index !== false){
            this.remove(index);
        }
    }
    return this;
}

//交集
Array.prototype.intersection = function(arr){
    if(!arr.isArray()){
        return ;
    }
    var intersectionArr = [];
    for(var i = 0, len = arr.length; i < len; i++){
        if(this.index(arr[i]) !== false){
            intersectionArr.push(arr[i]);
        }
    }
    return intersectionArr;
}

//各元素不重复
Array.prototype.unique = function(){
    for(var i = 1, len = this.length; i < len; i++){
        for(var j = 0; j < i; j++){
            if(this[i] == this[j]){
                this.remove(i);
            }
        }
    }
    return this;
}