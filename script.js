(() => {
  const body = document.body;
  const leftColumn = document.querySelector('#left-column');
  const rightColumn = document.querySelector('#right-column');
  const matchCounter = document.querySelector('#match-counter');
  const resetBtn = document.querySelector('#reset-btn');
  const toast = document.querySelector('#toast');
  const draggableTemplate = document.querySelector('#draggable-card-template');
  const droppableTemplate = document.querySelector('#droppable-card-template');

  const gameState = {
    totalPairs: 0,
    matches: 0,
    items: [],
    itemMap: new Map(),
    mode: 'list',
    singleQueue: [],
    currentIndex: 0,
    targetCard: null,
  };

  function loadConfig() {
    return new Promise((resolve, reject) => {
      const inlineConfig = window.MATCH_GAME_CONFIG;

      if (inlineConfig && Array.isArray(inlineConfig.items)) {
        resolve(inlineConfig);
        return;
      }

      if (typeof fetch !== 'function') {
        reject(new Error('No se encontró configuración disponible.'));
        return;
      }

      fetch('config/config.json')
        .then((response) => {
          if (!response.ok) {
            throw new Error(`Error al cargar configuración: ${response.status}`);
          }
          return response.json();
        })
        .then(resolve)
        .catch((error) => {
          reject(new Error(error.message || 'No se pudo cargar la configuración.'));
        });
    });
  }

  function shuffle(array) {
    const arr = [...array];
    for (let i = arr.length - 1; i > 0; i -= 1) {
      const j = Math.floor(Math.random() * (i + 1));
      [arr[i], arr[j]] = [arr[j], arr[i]];
    }
    return arr;
  }

  function normalizeItems(items) {
    if (!Array.isArray(items)) {
      return [];
    }

    return items.map((item, index) => {
      const safeItem = item || {};
      const id = safeItem.id != null ? String(safeItem.id) : `item-${index}`;
      const caption =
        safeItem.caption || safeItem.left || safeItem.spanish || `Tarjeta ${index + 1}`;
      const english = safeItem.english || safeItem.right || '';
      const detail = safeItem.detail || safeItem.description || '';
      const image = safeItem.image || 'assets/placeholder.svg';
      const altText =
        safeItem.alt ||
        caption ||
        detail ||
        english ||
        'Ilustración asociada al concepto';

      return {
        id,
        caption,
        english,
        detail,
        image,
        alt: altText,
      };
    });
  }

  function applyModeClasses(mode) {
    gameState.mode = mode === 'single' ? 'single' : 'list';
    body.dataset.mode = gameState.mode;
  }

  function renderBoard(items) {
    if (gameState.mode === 'single') {
      renderSingleBoard(items);
      return;
    }

    applyModeClasses('list');

    leftColumn.innerHTML = '';
    rightColumn.innerHTML = '';
    matchCounter.textContent = '0';
    toast.classList.remove('visible');

    gameState.matches = 0;
    gameState.totalPairs = items.length;
    gameState.items = items;
    gameState.itemMap = new Map(items.map((item) => [item.id, item]));
    gameState.targetCard = null;
    gameState.singleQueue = [];
    gameState.currentIndex = 0;

    const shuffledLeft = shuffle(items);
    const shuffledRight = shuffle(items);

    shuffledLeft.forEach((item) => {
      const card = draggableTemplate.content.firstElementChild.cloneNode(true);
      const image = card.querySelector('.card-image');
      const title = card.querySelector('.card-title');
      const description = card.querySelector('.card-description');

      image.src = item.image;
      image.alt = item.alt;
      title.textContent = item.caption;

      if (item.detail) {
        description.textContent = item.detail;
        description.hidden = false;
      } else {
        description.hidden = true;
      }

      card.dataset.id = item.id;
      card.setAttribute('aria-label', `Arrastrar ${item.caption}`);

      card.addEventListener('dragstart', handleDragStart);
      card.addEventListener('dragend', handleDragEnd);

      leftColumn.appendChild(card);
    });

    shuffledRight.forEach((item) => {
      const card = droppableTemplate.content.firstElementChild.cloneNode(true);
      const title = card.querySelector('.card-title');
      const subtitle = card.querySelector('.card-subtitle');
      const preview = card.querySelector('.match-preview');

      title.textContent = item.english;
      subtitle.textContent = 'Arrastra aquí';
      preview.hidden = true;

      card.dataset.targetId = item.id;
      card.dataset.defaultSubtitle = 'Arrastra aquí';
      card.setAttribute('aria-label', `Objetivo: ${item.english}`);

      card.addEventListener('dragover', handleDragOver);
      card.addEventListener('drop', handleDrop);

      rightColumn.appendChild(card);
    });
  }

  function renderSingleBoard(items) {
    applyModeClasses('single');

    leftColumn.innerHTML = '';
    rightColumn.innerHTML = '';
    matchCounter.textContent = '0';
    toast.classList.remove('visible');

    gameState.matches = 0;
    gameState.totalPairs = items.length;
    gameState.items = items;
    gameState.itemMap = new Map(items.map((item) => [item.id, item]));
    gameState.singleQueue = shuffle(items);
    gameState.currentIndex = 0;

    const optionsOrder = shuffle(items);

    optionsOrder.forEach((item) => {
      const card = draggableTemplate.content.firstElementChild.cloneNode(true);
      const media = card.querySelector('.card-media');
      const title = card.querySelector('.card-title');
      const description = card.querySelector('.card-description');

      if (media) {
        media.remove();
      }

      card.classList.add('text-option');
      title.textContent = item.english;
      description.hidden = true;

      card.dataset.id = item.id;
      card.setAttribute('aria-label', `Arrastra ${item.english}`);

      card.addEventListener('dragstart', handleDragStart);
      card.addEventListener('dragend', handleDragEnd);

      leftColumn.appendChild(card);
    });

    const targetCard = droppableTemplate.content.firstElementChild.cloneNode(true);
    targetCard.classList.add('single-target-card');

    targetCard.addEventListener('dragover', handleDragOver);
    targetCard.addEventListener('drop', handleDrop);

    rightColumn.appendChild(targetCard);
    gameState.targetCard = targetCard;

    updateSingleTargetCard();
  }

  function updateSingleTargetCard() {
    const targetCard = gameState.targetCard;
    if (!targetCard) return;

    const title = targetCard.querySelector('.card-title');
    const subtitle = targetCard.querySelector('.card-subtitle');
    const preview = targetCard.querySelector('.match-preview');
    const previewImage = targetCard.querySelector('.match-preview-image');
    const previewCaption = targetCard.querySelector('.match-preview-caption');

    const currentItem = gameState.singleQueue[gameState.currentIndex];

    if (!currentItem) {
      targetCard.dataset.targetId = '';
      targetCard.classList.add('completed');
      targetCard.classList.remove('mismatch');
      if (title) {
        title.textContent = '¡Increíble!';
      }
      if (subtitle) {
        subtitle.textContent = 'Terminaste todas las combinaciones.';
        targetCard.dataset.defaultSubtitle = subtitle.textContent;
      }
      if (preview) {
        preview.hidden = true;
      }
      targetCard.setAttribute('aria-label', 'Todas las combinaciones completadas.');
      return;
    }

    targetCard.dataset.targetId = currentItem.id;
    targetCard.classList.remove('matched', 'mismatch', 'completed');

    if (title) {
      title.textContent = currentItem.caption;
    }

    const subtitleText = currentItem.detail || 'Arrastra la palabra correcta';
    if (subtitle) {
      subtitle.textContent = subtitleText;
      targetCard.dataset.defaultSubtitle = subtitleText;
    }

    if (preview && previewImage && previewCaption) {
      preview.hidden = false;
      previewImage.src = currentItem.image;
      previewImage.alt = currentItem.alt;
      previewCaption.textContent = currentItem.detail || currentItem.caption;
    }

    targetCard.setAttribute(
      'aria-label',
      `Objetivo actual: ${currentItem.caption}. Arrastra su palabra en inglés.`
    );
  }

  function handleDragStart(event) {
    const card = event.currentTarget;
    card.classList.add('dragging');
    event.dataTransfer.setData('text/plain', card.dataset.id);
    event.dataTransfer.effectAllowed = 'move';
  }

  function handleDragEnd(event) {
    event.currentTarget.classList.remove('dragging');
  }

  function handleDragOver(event) {
    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';
  }

  function handleDrop(event) {
    event.preventDefault();
    const targetCard = event.currentTarget;
    if (!targetCard.dataset.targetId) {
      return;
    }
    const draggedId = event.dataTransfer.getData('text/plain');
    evaluateMatch(draggedId, targetCard);
  }

  function handleListSuccess(matchedItem, draggedCard, targetCard) {
    targetCard.classList.add('matched');
    targetCard.classList.remove('mismatch');

    const subtitle = targetCard.querySelector('.card-subtitle');
    const preview = targetCard.querySelector('.match-preview');
    const previewImage = targetCard.querySelector('.match-preview-image');
    const previewCaption = targetCard.querySelector('.match-preview-caption');

    if (subtitle) {
      subtitle.textContent = matchedItem ? matchedItem.caption : '¡Perfecto!';
    }

    if (matchedItem && preview && previewImage && previewCaption) {
      previewImage.src = matchedItem.image;
      previewImage.alt = matchedItem.alt;
      previewCaption.textContent = matchedItem.detail || matchedItem.caption;
      preview.hidden = false;
    }

    targetCard.setAttribute(
      'aria-label',
      matchedItem
        ? `Match completo: ${matchedItem.english} significa ${matchedItem.caption}`
        : 'Match completo'
    );

    draggedCard.classList.add('matched');
    draggedCard.setAttribute('draggable', 'false');
    draggedCard.style.cursor = 'default';
    draggedCard.remove();

    gameState.matches += 1;
    matchCounter.textContent = gameState.matches.toString();

    showToast('¡Match logrado! ✨');

    if (gameState.matches === gameState.totalPairs) {
      showToast('¡Victoria! Todas las combinaciones correctas. 🎉');
    }
  }

  function handleSingleSuccess(matchedItem, draggedCard, targetCard) {
    if (!matchedItem) {
      return;
    }

    draggedCard.classList.add('matched');
    draggedCard.setAttribute('draggable', 'false');
    draggedCard.style.cursor = 'default';
    draggedCard.setAttribute('aria-label', `${matchedItem.english} ya fue emparejado`);

    targetCard.classList.add('matched');
    targetCard.classList.remove('mismatch');
    targetCard.dataset.targetId = '';

    const subtitle = targetCard.querySelector('.card-subtitle');
    if (subtitle) {
      subtitle.textContent = `${matchedItem.english} ✔️`;
    }

    showToast('¡Match logrado! ✨');

    gameState.matches += 1;
    matchCounter.textContent = gameState.matches.toString();

    setTimeout(() => {
      gameState.currentIndex += 1;
      targetCard.classList.remove('matched');

      if (gameState.matches === gameState.totalPairs) {
        updateSingleTargetCard();
        showToast('¡Victoria! Todas las combinaciones correctas. 🎉');
      } else {
        updateSingleTargetCard();
      }
    }, 600);
  }

  function handleMismatch(targetCard) {
    targetCard.classList.add('mismatch');
    const subtitle = targetCard.querySelector('.card-subtitle');
    if (subtitle) {
      subtitle.textContent = 'Sigue intentando';
    }
    setTimeout(() => {
      targetCard.classList.remove('mismatch');
      if (!targetCard.classList.contains('matched') && subtitle) {
        subtitle.textContent = targetCard.dataset.defaultSubtitle || 'Arrastra aquí';
      }
    }, 600);
    showToast('Oops, intenta otra combinación.', true);
  }

  function evaluateMatch(draggedId, targetCard) {
    if (!targetCard) return;
    const targetId = targetCard.dataset.targetId;
    const draggedCard = document.querySelector(`.draggable[data-id="${draggedId}"]`);

    if (!draggedCard) return;

    if (draggedId === targetId) {
      const matchedItem = gameState.itemMap.get(targetId);

      if (gameState.mode === 'single') {
        handleSingleSuccess(matchedItem, draggedCard, targetCard);
      } else if (!targetCard.classList.contains('matched')) {
        handleListSuccess(matchedItem, draggedCard, targetCard);
      }
    } else {
      handleMismatch(targetCard);
    }
  }

  function showToast(message, isError = false) {
    toast.textContent = message;
    toast.style.borderColor = isError ? 'rgba(255, 107, 107, 0.6)' : 'rgba(122, 252, 255, 0.6)';
    toast.classList.add('visible');
    setTimeout(() => toast.classList.remove('visible'), 1500);
  }

  function initGame() {
    loadConfig()
      .then(({ displayCount, layoutMode, items }) => {
        const normalized = normalizeItems(items);
        if (normalized.length === 0) {
          leftColumn.innerHTML = '<p>No hay tarjetas configuradas.</p>';
          rightColumn.innerHTML = '';
          matchCounter.textContent = '0';
          showToast('No hay tarjetas configuradas.', true);
          return;
        }
        const maxItems = Math.max(1, displayCount == null ? normalized.length : displayCount);
        const limited = Math.min(maxItems, normalized.length);
        const selectedItems = shuffle(normalized).slice(0, limited);
        const requestedMode =
          typeof layoutMode === 'string' && layoutMode.toLowerCase() === 'single'
            ? 'single'
            : 'list';
        gameState.mode = requestedMode;
        renderBoard(selectedItems);
      })
      .catch((error) => {
        leftColumn.innerHTML = '<p>No se pudo cargar el juego.</p>';
        rightColumn.innerHTML = '';
        showToast(error && error.message ? error.message : 'No se pudo cargar el juego.', true);
      });
  }

  resetBtn.addEventListener('click', initGame);

  document.addEventListener('DOMContentLoaded', initGame);
})();
