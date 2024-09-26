window.onload = function(){
    document.getElementById("btnPdf")
    .addEventListener("click", ()=>{
        const order = this.document.getElementById("print");
        const name = this.document.getElementById("header3").innerText;
        console.log(order);
        var opt = {
            margin:        0,
            filename:     name+'.pdf',
            image:        { type: 'jpeg'},
            html2canvas: { scale: 3, y: 0, scrollY: 0},
            jsPDF:        { format: 'A4', orientation: 'portrait' }
          };
        html2pdf().from(order).set(opt).save();
    })
}