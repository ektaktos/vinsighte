// document.addEventListener("DOMContentLoaded", function(event) {
//   $('#imageInput').on('change', (e) => {
//     console.log(e);
//     const image = e.target.files[0];
//     const reader = new FileReader();
//     reader.readAsDataURL(image);
//     reader.onload = (e2) => {
//       $('#imagePreview').attr('src', e2.target.result);
//     };
//     $('#imagePreviewBox').css('display', 'block');
//   });
// })