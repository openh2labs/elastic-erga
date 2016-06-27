var path = 'build/js/components/my-component-2/build/';
var img = new Image();
img.src = [path,'test.jpeg'].join('');

document.querySelector('.div1').appendChild(img);
