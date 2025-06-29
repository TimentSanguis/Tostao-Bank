window.addEventListener('load', () => {
  const welcome = document.getElementById('welcome-screen');

  const hideWelcome = () => {
    if (!welcome.classList.contains('fade-out')) {
      welcome.classList.add('fade-out');
      setTimeout(() => {
        welcome.style.display = 'none';
      }, 270); // tempo da transição em ms
    }
  };

  // Sai automaticamente após 4s
  setTimeout(hideWelcome, 4000);

  // Ou ao clicar na tela
  welcome.addEventListener('click', hideWelcome);
});