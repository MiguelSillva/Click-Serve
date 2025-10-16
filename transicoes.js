// Arquivo: transicoes.js

document.addEventListener('DOMContentLoaded', () => {
  // Seleciona todos os links de navegação que devem ter a animação
  const navLinks = document.querySelectorAll('.nav-link');

  navLinks.forEach(link => {
    link.addEventListener('click', function (event) {
      // Pega o endereço para onde o link aponta
      const destination = this.href;

      // Se o link for para a mesma página, não faz nada
      if (destination === window.location.href) {
        return;
      }
      
      // Previne a navegação imediata
      event.preventDefault();

      // Adiciona a classe que dispara a animação de fade-out
      document.body.classList.add('fade-out');

      // Espera a animação terminar (400ms, o mesmo tempo da transição no CSS)
      setTimeout(() => {
        // Navega para o novo endereço
        window.location.href = destination;
      }, 400);
    });
  });
});