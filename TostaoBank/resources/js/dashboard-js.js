let btn = document.querySelector("#btn");
let colleft = document.querySelector(".column-left");
let colmid = document.querySelector(".column-middle");
let colright = document.querySelector(".column-right");

function myFunction() {
  colleft.classList.toggle("active");
  colmid.classList.toggle("active");
  colright.classList.toggle("active");
}

// Copiar número do cartão
const optionElement = document.querySelector('#copy-card');
if (optionElement) {
  optionElement.addEventListener('click', () => {
    const text = document.querySelector('#card-number')?.textContent;
    if (text) {
      navigator.clipboard.writeText(text);
      alert('Copiado para área de tranfêrencia!');
    }
  });
}

// Botão de senha online
const optionElement2 = document.querySelector('#online-pass-button');
if (optionElement2) {
  optionElement2.addEventListener('click', () => {
    alert('Você receberá um SMS com sua senha em breve.');
  });
}

// Botão ativar/desativar status
function toggledeactive() {
  var button = document.getElementById("active-deactive-button");
  var activespan = document.getElementById("activespan");
  var status = document.getElementById("status");

  if (!button || !activespan || !status) return;

  if (button.classList.contains("active")) {
    button.classList.remove("active");
    button.style.backgroundColor = "#f45050";
    button.innerHTML = "Desativar";
    activespan.style.color = "green";
    activespan.innerHTML = "Ativo";
    status.style.border = "3px solid green";
  } else {
    button.classList.add("active");
    button.style.backgroundColor = "green";
    button.innerHTML = "Ativar";
    activespan.style.color = "#f45050";
    activespan.innerHTML = "Desativado";
    status.style.border = "3px solid red";
  }
}

// Atualizar saldo via API
function atualizarSaldoViaAPI() {
  const token = sessionStorage.getItem('token');
  if (!token) {
    console.warn('Token não encontrado. Usuário pode estar deslogado.');
    return;
  }

  fetch('http://localhost:8080/tostaoBank/saldo', {
    method: 'GET',
    headers: {
      'Authorization': `Bearer ${token}`
    }
  })
    .then(response => {
      if (!response.ok) throw new Error('Erro ao buscar saldo');
      return response.json();
    })
    .then(data => {
      if (data.saldo) {
        const saldo = parseFloat(data.saldo).toFixed(2).replace('.', ',');
        const saldoDisplay = document.getElementById('saldoDisplay');
        const saldoValue = document.getElementById('saldoValue');

        if (saldoDisplay && saldoValue) {
          saldoDisplay.innerHTML = `Seu saldo: R$ ${saldo}`;
          saldoValue.innerHTML = saldo;
        }
      }
    })
    .catch(error => {
      console.error('Erro ao atualizar saldo via API:', error);
      const saldoDisplay = document.getElementById('saldoDisplay');
      if (saldoDisplay) {
        saldoDisplay.innerHTML = 'Erro ao buscar saldo';
      }
    });
}

document.addEventListener('DOMContentLoaded', () => {
  atualizarSaldoViaAPI();
  setInterval(atualizarSaldoViaAPI, 10000); // atualiza a cada 10s
});