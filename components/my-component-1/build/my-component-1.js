var path = 'build/js/components/my-component-1/build/';
var img = new Image();
img.src = [path,'test.png'].join('');

document.querySelector('.div2').appendChild(img);
