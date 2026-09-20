(function () {
    'use strict';

    /* ---------- Mostrar / ocultar senha ---------- */
    document.querySelectorAll('[data-toggle-senha]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = btn.parentElement.querySelector('input');
            var visivel = input.type === 'password';
            input.type = visivel ? 'text' : 'password';
            btn.setAttribute('aria-pressed', String(visivel));
            btn.setAttribute('aria-label', visivel ? 'Ocultar senha' : 'Mostrar senha');
        });
    });

    /* ---------- Máscara de telefone: (11) 99999-9999 ---------- */
    function mascaraTelefone(valor) {
        var d = valor.replace(/\D/g, '').slice(0, 11);
        if (d.length <= 2) return d ? '(' + d : '';
        if (d.length <= 6) return '(' + d.slice(0, 2) + ') ' + d.slice(2);
        if (d.length <= 10) return '(' + d.slice(0, 2) + ') ' + d.slice(2, 6) + '-' + d.slice(6);
        return '(' + d.slice(0, 2) + ') ' + d.slice(2, 7) + '-' + d.slice(7);
    }
    document.querySelectorAll('[data-mask="telefone"]').forEach(function (input) {
        input.addEventListener('input', function () {
            input.value = mascaraTelefone(input.value);
        });
        if (input.value) input.value = mascaraTelefone(input.value);
    });

    /* ---------- "Esqueceu sua senha?" ---------- */
    var esqueci = document.querySelector('[data-esqueci]');
    if (esqueci) {
        esqueci.addEventListener('click', function () {
            var aviso = document.getElementById(esqueci.getAttribute('aria-controls'));
            if (aviso) aviso.hidden = !aviso.hidden;
        });
    }

    /* ---------- Confirmar presença ---------- */
    var btnPresenca = document.querySelector('[data-confirmar-presenca]');
    if (btnPresenca) {
        btnPresenca.addEventListener('click', function () {
            var csrf = document.querySelector('meta[name="csrf"]').content;
            var rotulo = btnPresenca.querySelector('[data-rotulo]');
            btnPresenca.disabled = true;

            fetch('api/presenca.php', {
                method: 'POST',
                headers: { 'X-CSRF-Token': csrf, 'Accept': 'application/json' },
                credentials: 'same-origin'
            })
                .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, dados: j }; }); })
                .then(function (res) {
                    if (!res.ok || !res.dados.ok) throw new Error(res.dados.erro || 'Não foi possível confirmar.');
                    rotulo.textContent = 'Presença confirmada';
                    atualizarProgresso(res.dados.total, res.dados.meta);
                })
                .catch(function (err) {
                    btnPresenca.disabled = false;
                    mostrarToast(err.message);
                });
        });
    }

    function atualizarProgresso(total, meta) {
        var texto = document.querySelector('[data-progresso-texto]');
        var barra = document.querySelector('[data-barra]');
        if (texto) texto.textContent = total + '/' + meta + ' treinos concluídos';
        if (barra) {
            barra.setAttribute('aria-valuenow', total);
            barra.firstElementChild.style.width = (total / meta) * 100 + '%';
        }
        document.querySelectorAll('.dia').forEach(function (dia, i) {
            dia.classList.toggle('is-feito', i < total);
        });
    }

    /* ---------- Prévia da foto de perfil ---------- */
    var inputFoto = document.querySelector('[data-avatar-input]');
    if (inputFoto) {
        inputFoto.addEventListener('change', function () {
            var erro = document.querySelector('[data-avatar-erro]');
            var atual = document.querySelector('[data-avatar-atual]');
            var previa = document.querySelector('[data-avatar-previa]');
            var arquivo = inputFoto.files && inputFoto.files[0];
            erro.hidden = true;
            if (!arquivo) return;

            if (!/^image\/(jpeg|png|webp)$/.test(arquivo.type)) {
                erro.textContent = 'Use uma imagem JPG, PNG ou WEBP.';
                erro.hidden = false;
                inputFoto.value = '';
                return;
            }
            if (arquivo.size > 2 * 1024 * 1024) {
                erro.textContent = 'A foto deve ter no máximo 2 MB.';
                erro.hidden = false;
                inputFoto.value = '';
                return;
            }
            previa.src = URL.createObjectURL(arquivo);
            previa.hidden = false;
            atual.hidden = true;
        });
    }

    /* ---------- Toast ---------- */
    function mostrarToast(msg) {
        var t = document.createElement('div');
        t.className = 'toast';
        t.setAttribute('role', 'status');
        t.textContent = msg;
        document.body.appendChild(t);
        esconderToast(t);
    }
    function esconderToast(t) {
        setTimeout(function () {
            t.classList.add('is-saindo');
            setTimeout(function () { t.remove(); }, 350);
        }, 4000);
    }
    document.querySelectorAll('[data-toast]').forEach(esconderToast);
})();
