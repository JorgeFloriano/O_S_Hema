window.onload = function(){
    document.getElementById("btnPdf")
    .addEventListener("click", ()=>{
        const order = this.document.getElementById("print");
        const name = this.document.getElementById("header3").innerText;
        const isNotMobile = window.innerWidth > 768;
        const margin = isNotMobile ? [0, -3, 0, 0] : [0, 0, 0, 0];
        console.log(order);
        var opt = {
            margin: margin,
            filename:     name+'.pdf',
            image:        { type: 'jpeg'},
            html2canvas: { scale: 3, y: 0, scrollY: 0},
            jsPDF:        { format: 'A4', orientation: 'portrait' },
            pagebreak: { before: '.page-break' },
            x: window.innerWidth / 2 - order.offsetWidth / 2 // center horizontally
          };
        html2pdf().from(order).set(opt).save();
    })
}