let slideIndex = 0;
let slides = document.getElementsByClassName("slide");

function showSlides() {
  for (let i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";  // Oculta todos os slides
  }
  slideIndex++;
  if (slideIndex > slides.length) {slideIndex = 1}    
  slides[slideIndex-1].style.display = "block";  // Exibe o slide atual
  setTimeout(showSlides, 2000); // Altera o slide a cada 2 segundos
}

showSlides(); // Inicia o carrossel de slides
document.addEventListener("DOMContentLoaded", function() {
  const slider = document.querySelector(".barbearia-slider");
  const prevButton = document.querySelector(".prev");
  const nextButton = document.querySelector(".next");

  let scrollAmount = 0;
  const step = 200; // Define o valor de movimento do slider

  // Função para rolar o slider para a direita
  function slideRight() {
      scrollAmount += step;
      if (scrollAmount > slider.scrollWidth - slider.clientWidth) {
          scrollAmount = slider.scrollWidth - slider.clientWidth;
      }
      slider.scrollTo({
          top: 0,
          left: scrollAmount,
          behavior: "smooth"
      });
  }

  // Função para rolar o slider para a esquerda
  function slideLeft() {
      scrollAmount -= step;
      if (scrollAmount < 0) {
          scrollAmount = 0;
      }
      slider.scrollTo({
          top: 0,
          left: scrollAmount,
          behavior: "smooth"
      });
  }

  // Adiciona eventos de clique aos botões de navegação
  prevButton.addEventListener("click", slideLeft);
  nextButton.addEventListener("click", slideRight);
});
