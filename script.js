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
};

async function loadConfig() {
  try {
    const response = await fetch('config/config.json');
    if (!response.ok) {
      throw new Error(`Error al cargar configuración: ${response.status}`);
    }
    return response.json();
  } catch (error) {
    showToast(error.message, true);
    throw error;
  }
}

function shuffle(array) {
  const arr = [...array];
  for (let i = arr.length - 1; i > 0; i -= 1) {
    const j = Math.floor(Math.random() * (i + 1));
    [arr[i], arr[j]] = [arr[j], arr[i]];
  }
  return arr;
}

function getEmojiFromLabel(label) {
  const match = label.match(/^(\p{Emoji_Presentation}|\p{Extended_Pictographic})/u);
  return match ? match[0] : '✨';
}

function renderBoard(items) {
  leftColumn.innerHTML = '';
  rightColumn.innerHTML = '';
  matchCounter.textContent = '0';
  gameState.matches = 0;
  gameState.totalPairs = items.length;
  gameState.items = items;

  const shuffledLeft = shuffle(items);
  const shuffledRight = shuffle(items);

  shuffledLeft.forEach((item) => {
    const card = draggableTemplate.content.firstElementChild.cloneNode(true);
    const emojiSpan = card.querySelector('.emoji');
    const title = card.querySelector('.card-title');
    const emoji = getEmojiFromLabel(item.left);

    emojiSpan.textContent = emoji;
    title.textContent = item.left.replace(emoji, '').trim() || item.left;

    card.dataset.id = item.id;
    card.setAttribute('aria-label', `Arrastrar ${item.left}`);

    card.addEventListener('dragstart', handleDragStart);
    card.addEventListener('dragend', handleDragEnd);

    leftColumn.appendChild(card);
  });

  shuffledRight.forEach((item) => {
    const card = droppableTemplate.content.firstElementChild.cloneNode(true);
    const title = card.querySelector('.card-title');
    title.textContent = item.right;
    card.dataset.targetId = item.id;
    card.setAttribute('aria-label', `Objetivo: ${item.right}`);

    card.addEventListener('dragover', handleDragOver);
    card.addEventListener('drop', handleDrop);

    rightColumn.appendChild(card);
  });
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
  const draggedId = event.dataTransfer.getData('text/plain');
  evaluateMatch(draggedId, targetCard);
}

function evaluateMatch(draggedId, targetCard) {
  const targetId = targetCard.dataset.targetId;
  const draggedCard = document.querySelector(`.draggable[data-id="${draggedId}"]`);

  if (!draggedCard) return;

  if (draggedId === targetId && !targetCard.classList.contains('matched')) {
    targetCard.classList.add('matched');
    targetCard.querySelector('.card-subtitle').textContent = '¡Perfecto!';

    draggedCard.classList.add('matched');
    draggedCard.setAttribute('draggable', 'false');
    draggedCard.style.cursor = 'default';

    const clone = draggedCard.cloneNode(true);
    clone.classList.remove('dragging');
    clone.setAttribute('draggable', 'false');
    targetCard.appendChild(clone);

    draggedCard.remove();

    gameState.matches += 1;
    matchCounter.textContent = gameState.matches.toString();

    showToast('¡Match logrado! ✨');

    if (gameState.matches === gameState.totalPairs) {
      showToast('¡Victoria! Todas las vibes combinan. 🎉');
    }
  } else {
    targetCard.classList.add('mismatch');
    setTimeout(() => targetCard.classList.remove('mismatch'), 500);
    showToast('Oops, intenta otra combinación.', true);
  }
}

function showToast(message, isError = false) {
  toast.textContent = message;
  toast.style.borderColor = isError ? 'rgba(255, 107, 107, 0.6)' : 'rgba(122, 252, 255, 0.6)';
  toast.classList.add('visible');
  setTimeout(() => toast.classList.remove('visible'), 1500);
}

async function initGame() {
  const { displayCount, items } = await loadConfig();
  const maxItems = Math.max(1, displayCount ?? items.length);
  const selectedItems = shuffle(items).slice(0, Math.min(maxItems, items.length));
  renderBoard(selectedItems);
}

resetBtn.addEventListener('click', initGame);

document.addEventListener('DOMContentLoaded', () => {
  initGame().catch(() => {
    leftColumn.innerHTML = '<p>No se pudo cargar el juego.</p>';
  });
});
