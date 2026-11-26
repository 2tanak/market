export default str =>{
let result;
result = str.split('').reduce((res,n)=>{
 return res.replace('x',n)
},'x-(xxx)-xxx-xx-xx');

return result;
}



