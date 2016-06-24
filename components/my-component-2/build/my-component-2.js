var path = 'my-component-2/build/';
var img = new Image();
img.src = [path,'test.jpg'].join('');

document.getElementsByTagName('body')[0].appendChild(img);
