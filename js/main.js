/**
 * ==========================================================================
 * REFONTE EXPERNET CAMPUS - SCRIPTS INTERACTIFS (JS)
 * ==========================================================================
 * Ce script gère :
 * 1. Le filtrage dynamique et la recherche en temps réel du catalogue
 * 2. L'interactivité de signature de présence sur le tableau de bord étudiant
 * ==========================================================================
 */

// On attend que l'ensemble du DOM soit complètement chargé avant d'exécuter le script
document.addEventListener('DOMContentLoaded', () => {

  /* ========================================================================
     1. SYSTÈME DE FILTRES ET RECHERCHE DU CATALOGUE (catalogue.html)
     ======================================================================== */

  // Récupération du champ de saisie de recherche par mots-clés
  const searchInput = document.getElementById('search-formation');

  // Récupération des cases à cocher (checkboxes) des différents filtres
  const filiereCheckboxes = document.querySelectorAll('.filter-filiere'); // Filtres : Informatique, Marketing, Réseau
  const rncpCheckboxes = document.querySelectorAll('.filter-rncp');       // Filtres : Niveaux 5, 6, 7
  const cpfCheckboxes = document.querySelectorAll('.filter-cpf');         // Filtres : Oui, Non

  // Récupération de toutes les fiches de formations affichées sur la page
  const formationCards = document.querySelectorAll('.formation-card');

  // Message d'alerte affiché si aucun résultat ne correspond aux filtres
  const noResultMsg = document.getElementById('no-result-msg');

  // On n'active la logique de filtrage que si nous sommes sur la page catalogue
  if (formationCards.length > 0) {

    /**
     * Fonction principale de filtrage
     * Parcourt chaque carte de formation et détermine si elle doit être visible
     * en croisant le texte recherché et l'état de chaque groupe de filtres.
     */
    function filterFormations() {
      // 1. Récupère le texte tapé par l'utilisateur (en minuscules pour éviter la sensibilité à la casse)
      const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

      // 2. Récupère la liste des filières actuellement cochées (ex: ['informatique', 'reseau'])
      const selectedFilieres = Array.from(filiereCheckboxes)
        .filter(checkbox => checkbox.checked)
        .map(checkbox => checkbox.value.toLowerCase());

      // 3. Récupère la liste des niveaux RNCP cochés (ex: ['5', '6'])
      const selectedRncps = Array.from(rncpCheckboxes)
        .filter(checkbox => checkbox.checked)
        .map(checkbox => checkbox.value.toLowerCase());

      // 4. Récupère la liste des choix CPF cochés (ex: ['oui'])
      const selectedCpfs = Array.from(cpfCheckboxes)
        .filter(checkbox => checkbox.checked)
        .map(checkbox => checkbox.value.toLowerCase());

      // Compteur du nombre de formations visibles après application des filtres
      let visibleCount = 0;

      // 5. Parcourt chaque carte pour vérifier si elle satisfait TOUS les critères
      formationCards.forEach(card => {
        const text = card.textContent.toLowerCase();
        const filiere = (card.dataset.filiere || '').toLowerCase(); // Attribut HTML data-filiere
        const rncp = (card.dataset.rncp || '').toLowerCase();       // Attribut HTML data-rncp
        const cpf = (card.dataset.cpf || '').toLowerCase();         // Attribut HTML data-cpf

        // Vérification de la correspondance avec la recherche textuelle
        const matchSearch = !searchTerm || text.includes(searchTerm);

        // Vérification avec les filières (si aucune case cochée, on accepte tout)
        const matchFiliere = selectedFilieres.length === 0 || selectedFilieres.includes(filiere);

        // Vérification avec le niveau RNCP
        const matchRncp = selectedRncps.length === 0 || selectedRncps.includes(rncp);

        // Vérification avec l'éligibilité CPF
        const matchCpf = selectedCpfs.length === 0 || selectedCpfs.includes(cpf);

        // Si la carte correspond à TOUS les filtres actifs, on l'affiche
        if (matchSearch && matchFiliere && matchRncp && matchCpf) {
          card.classList.remove('d-none'); // Retire la classe Bootstrap d-none (display: none)
          visibleCount++;
        } else {
          card.classList.add('d-none');    // Cache la carte en ajoutant la classe Bootstrap d-none
        }
      });

      // 6. Gestion du message "Aucun résultat"
      if (noResultMsg) {
        if (visibleCount === 0) {
          noResultMsg.classList.remove('d-none'); // Affiche le message
        } else {
          noResultMsg.classList.add('d-none');    // Masque le message
        }
      }
    }

    // Écouteur d'événement sur la saisie dans le champ de recherche (déclenché à chaque frappe)
    if (searchInput) {
      searchInput.addEventListener('input', filterFormations);
    }

    // Écouteurs d'événements sur le changement d'état de chaque case à cocher
    filiereCheckboxes.forEach(cb => cb.addEventListener('change', filterFormations));
    rncpCheckboxes.forEach(cb => cb.addEventListener('change', filterFormations));
    cpfCheckboxes.forEach(cb => cb.addEventListener('change', filterFormations));

    // Gestion du bouton de réinitialisation de tous les filtres
    const resetBtn = document.getElementById('reset-filters');
    if (resetBtn) {
      resetBtn.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';             // Vide le champ texte
        filiereCheckboxes.forEach(cb => cb.checked = false); // Décoche les filières
        rncpCheckboxes.forEach(cb => cb.checked = false);    // Décoche les RNCP
        cpfCheckboxes.forEach(cb => cb.checked = false);     // Décoche le CPF
        filterFormations();                                  // Réapplique le filtre pour tout réafficher
      });
    }
  }

  /* ========================================================================
     2. GESTION DE L'ÉMARGEMENT / SIGNATURE (dashboard-etudiant.html)
     ======================================================================== */

  // Récupération du bouton de signature de présence et du texte de statut
  const signBtn = document.getElementById('btn-signer-presence');
  const signStatus = document.getElementById('presence-status');

  if (signBtn) {
    // Écouteur au clic sur le bouton de signature
    signBtn.addEventListener('click', () => {
      // 1. Récupération de l'heure courante (format 24h, ex: 09:42)
      const now = new Date();
      const heure = now.toLocaleTimeString('fr-FR', { 
        hour: '2-digit', 
        minute: '2-digit' 
      });

      // 2. Désactive le bouton pour empêcher une double signature
      signBtn.disabled = true;

      // 3. Met à jour le style visuel du bouton (devient vert avec la classe Bootstrap btn-success)
      signBtn.className = 'btn btn-success fw-bold px-4 py-2 font-montserrat';
      signBtn.innerHTML = `✓ Présence enregistrée à ${heure}`;

      // 4. Met à jour le message de statut sous l'indicateur d'assiduité
      if (signStatus) {
        signStatus.textContent = 'Statut : Présent pour la session du jour';
        signStatus.classList.remove('text-muted');
        signStatus.classList.add('text-success', 'fw-bold');
      }
    });
  }

});
