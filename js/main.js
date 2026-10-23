/**
 * Refonte Expernet Campus - Scripts interactifs
 */
document.addEventListener('DOMContentLoaded', () => {
  // === FILTRE ET RECHERCHE DYNAMIQUE DU CATALOGUE ===
  const searchInput = document.getElementById('search-formation');
  const filiereCheckboxes = document.querySelectorAll('.filter-filiere');
  const rncpCheckboxes = document.querySelectorAll('.filter-rncp');
  const cpfCheckboxes = document.querySelectorAll('.filter-cpf');
  const formationCards = document.querySelectorAll('.formation-card');
  const noResultMsg = document.getElementById('no-result-msg');

  if (formationCards.length > 0) {
    function filterFormations() {
      const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

      const selectedFilieres = Array.from(filiereCheckboxes)
        .filter(cb => cb.checked)
        .map(cb => cb.value.toLowerCase());

      const selectedRncps = Array.from(rncpCheckboxes)
        .filter(cb => cb.checked)
        .map(cb => cb.value.toLowerCase());

      const selectedCpfs = Array.from(cpfCheckboxes)
        .filter(cb => cb.checked)
        .map(cb => cb.value.toLowerCase());

      let visibleCount = 0;

      formationCards.forEach(card => {
        const text = card.textContent.toLowerCase();
        const filiere = (card.dataset.filiere || '').toLowerCase();
        const rncp = (card.dataset.rncp || '').toLowerCase();
        const cpf = (card.dataset.cpf || '').toLowerCase();

        const matchSearch = !searchTerm || text.includes(searchTerm);
        const matchFiliere = selectedFilieres.length === 0 || selectedFilieres.includes(filiere);
        const matchRncp = selectedRncps.length === 0 || selectedRncps.includes(rncp);
        const matchCpf = selectedCpfs.length === 0 || selectedCpfs.includes(cpf);

        if (matchSearch && matchFiliere && matchRncp && matchCpf) {
          card.classList.remove('d-none');
          visibleCount++;
        } else {
          card.classList.add('d-none');
        }
      });

      if (noResultMsg) {
        if (visibleCount === 0) {
          noResultMsg.classList.remove('d-none');
        } else {
          noResultMsg.classList.add('d-none');
        }
      }
    }

    if (searchInput) {
      searchInput.addEventListener('input', filterFormations);
    }
    filiereCheckboxes.forEach(cb => cb.addEventListener('change', filterFormations));
    rncpCheckboxes.forEach(cb => cb.addEventListener('change', filterFormations));
    cpfCheckboxes.forEach(cb => cb.addEventListener('change', filterFormations));

    const resetBtn = document.getElementById('reset-filters');
    if (resetBtn) {
      resetBtn.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        filiereCheckboxes.forEach(cb => cb.checked = false);
        rncpCheckboxes.forEach(cb => cb.checked = false);
        cpfCheckboxes.forEach(cb => cb.checked = false);
        filterFormations();
      });
    }
  }

  // === SIGNATURE DE PRÉSENCE (DASHBOARD ÉTUDIANT) ===
  const signBtn = document.getElementById('btn-signer-presence');
  const signStatus = document.getElementById('presence-status');
  if (signBtn) {
    signBtn.addEventListener('click', () => {
      const now = new Date();
      const heure = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
      signBtn.disabled = true;
      signBtn.className = 'btn btn-success fw-bold px-4 py-2';
      signBtn.innerHTML = `✓ Présence enregistrée à ${heure}`;
      if (signStatus) {
        signStatus.textContent = 'Statut : Présent pour la session du jour';
        signStatus.classList.remove('text-muted');
        signStatus.classList.add('text-success', 'fw-bold');
      }
    });
  }
});
