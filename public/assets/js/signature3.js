const sc3 = document.querySelector('html');
const canvas3 = document.querySelector('#canv3');
const form3 = document.querySelector('#form');
const clearButton3 = document.querySelector('#clear3');
const signButton3 = document.querySelector('#pen3');
const okButton3 = document.querySelector('#okSign3');
const ctx3 = canvas3.getContext('2d');
const image3 = document.querySelector('#idSignCl');
let writingMode3 = false;
let writingModeBtn3 = false;
sc3.style.overflow = "";

form3.addEventListener('submit', () => {
    const image3URL = canvas3.toDataURL();
    image3.value = image3URL;
    image3.height = canvas3.height;
    image3.width = canvas3.width;
    image3.style.display = 'block';
    form3.appendChild(image3);
    clearPad3();
})

const clearPad3 = () => {
    ctx3.clearRect(0, 0, canvas3.width, canvas3.height);
}

clearButton3.addEventListener('click', (event) => {
    event.preventDefault();
    clearPad3();
})

signButton3.addEventListener('click', (event) => {
    event.preventDefault();
    writingModeBtn3 = true;
    //canvas3.style.boxShadow = " 0 0 0 .25rem rgba(13,110,253,.25)";
})

okButton3.addEventListener('click', (event) => {
    event.preventDefault();
    sc3.style.overflow = "";
    writingModeBtn3 = false;
    //canvas3.style.border = "1px solid #86b7fe";
    canvas3.style.boxShadow = "none";
})


const getTargetPositionMobile3 = (event) => {
    positionX = event.touches[0].clientX - event.target.getBoundingClientRect().x;
    positionY = event.touches[0].clientY - event.target.getBoundingClientRect().y;
  
    return [positionX, positionY];
  }

const getTargetPosition3 = (event) => {
    positionX = event.clientX - event.target.getBoundingClientRect().x;
    positionY = event.clientY - event.target.getBoundingClientRect().y;

    return [positionX, positionY];
}

const handlePointerMove3 = (event) => {
    if (writingModeBtn3) {
        if (!writingMode3) return
        if (event.type == 'touchmove') {
            const [positionX, positionY] = getTargetPositionMobile3(event);
        } else {
            const [positionX, positionY] = getTargetPosition3(event);
        }
        ctx3.lineTo(positionX, positionY);
        ctx3.stroke();
    }
}

const handlePointerUp3 = () => {
    writingMode3 = false;
}

const handlePointerDown3 = (event) => {
    writingMode3 = true;
    ctx3.beginPath();

    if (event.type == 'touchmove') {
        const [positionX, positionY] = getTargetPositionMobile3(event);
    } else {
        const [positionX, positionY] = getTargetPosition3(event);
    }
    ctx3.moveTo(positionX, positionY);
}

ctx3.lineWidth = 2;
ctx3.lineJoin = ctx3.lineCap = 'round';

canvas3.addEventListener('mousedown', handlePointerDown3, {passive: true});
canvas3.addEventListener('touchstart', handlePointerDown3, {passive: true});
canvas3.addEventListener('mouseup', handlePointerUp3, {passive: true});
canvas3.addEventListener('touchend', handlePointerUp3, {passive: true});
canvas3.addEventListener('mouseout', handlePointerUp3, {passive: true});
canvas3.addEventListener('mousemove', handlePointerMove3, {passive: true});
canvas3.addEventListener('touchmove', handlePointerMove3, {passive: true});