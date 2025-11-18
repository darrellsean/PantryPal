(() => {
const days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
const mealTypes = ['Breakfast','Lunch','Dinner','Snacks'];

const plannerContainer = document.getElementById('plannerContainer');
const weekInput = document.getElementById('plannerWeek');
const suggestionsList = document.getElementById('suggestionsList');
const savePlanBtn = document.getElementById('savePlanBtn');

const mealModal = document.getElementById('mealModal');
const modalTitle = document.getElementById('mealModalTitle');
const modalMealName = document.getElementById('modalMealName');
const modalRecipeSelect = document.getElementById('modalRecipeSelect');
const modalSaveMeal = document.getElementById('modalSaveMeal');
const modalCancel = document.getElementById('modalCancel');
const modalRecipeSection = document.querySelector('.modal-recipe-section');

let plans = {};
let inventory = [];
let suggestions = [];
let activeEditing = null;

function initEmptyPlans() {
    plans = {};
    days.forEach(d => {
        plans[d] = {};
        mealTypes.forEach(m => plans[d][m] = null);
    });
}

function buildPlannerUI() {
    plannerContainer.innerHTML = '';
    days.forEach(d => {
        const dayCard = document.createElement('div');
        dayCard.className = 'day-card';
        dayCard.innerHTML = `
            <div class="day-header">
                <h4>${d}</h4>
                <div class="day-actions"><small class="muted">Week: ${weekInput.value}</small></div>
            </div>
            <div class="slot-list" id="slots-${d}"></div>
        `;
        plannerContainer.appendChild(dayCard);

        const slots = dayCard.querySelector(`#slots-${d}`);
        mealTypes.forEach(m => {
            const slot = document.createElement('div');
            slot.className = 'meal-slot';
            slot.id = `slot-${d}-${m}`;
            const mealName = plans[d][m] ? 
                `<div class="meal-name">${escapeHtml(plans[d][m].custom_meal_name)}</div>` 
                : `<div class="meal-name muted">— no meal —</div>`;
            slot.innerHTML = `
                <div class="slot-left">${mealName}</div>
                <div class="meal-actions">
                    <button class="btn-secondary btn-edit" data-day="${d}" data-meal="${m}">Add / Edit</button>
                    <button class="btn-danger btn-clear" data-day="${d}" data-meal="${m}">Clear</button>
                </div>
            `;
            slots.appendChild(slot);
        });
    });

    // Edit buttons
    document.querySelectorAll('.btn-edit').forEach(b => b.addEventListener('click', (e)=>{
        const day = e.currentTarget.dataset.day;
        const meal = e.currentTarget.dataset.meal;
        openMealModal(day, meal);
    }));

    // Clear buttons
    document.querySelectorAll('.btn-clear').forEach(b => b.addEventListener('click', (e)=>{
        const day = e.currentTarget.dataset.day;
        const meal = e.currentTarget.dataset.meal;
        plans[day][meal] = null;
        updatePlannerUI();
    }));
}

function updatePlannerUI() {
    days.forEach(d => {
        mealTypes.forEach(m => {
            const node = document.getElementById(`slot-${d}-${m}`);
            if(!node) return;
            const content = node.querySelector('.slot-left');
            const item = plans[d][m];
            content.innerHTML = item ? `<div class="meal-name">${escapeHtml(item.custom_meal_name)}</div>` :
                                        `<div class="meal-name muted">— no meal —</div>`;
        });
    });
}

function fetchInventoryAndSuggestions() {
    // Fetch inventory
    fetch('api_meal_planner.php?action=list')
        .then(r => r.json())
        .then(data => {
            console.log('Fetched inventory:', data); // Debug
            inventory = Array.isArray(data.items) ? data.items : [];
            renderSuggestions();
        })
        .catch(err => {
            console.error('Inventory fetch error:', err);
            inventory = [];
            renderSuggestions();
        });

    // Fetch weekly meal plans
    const week_start = weekInput.value;
    fetch(`api_meal_planner.php?action=get_plans&week_start=${week_start}`)
        .then(r => r.json())
        .then(data => {
            initEmptyPlans();
            if (data.plans) {
                data.plans.forEach(p => {
                    if (plans[p.day]) plans[p.day][p.meal_type] = {custom_meal_name: p.custom_meal_name};
                });
            }
            buildPlannerUI();
            updatePlannerUI();
        })
        .catch(err => console.error('Meal plan fetch error:', err));
}

function renderSuggestions() {
    
    const today = new Date();

    // Expiring soon
    inventory.forEach(it => {
        if (!it.expiry_date) return;
        const [year, month, day] = it.expiry_date.split('-');
        const exp = new Date(year, month - 1, day); // month is 0-indexed
        const diff = Math.ceil((exp - today) / (1000*60*60*24));

        if (diff <= 3 && diff >= 0) { // only future or today
            suggestions.push({
                title: `Use ${it.item_name}`,
                reason: `${it.item_name} expires in ${diff} day(s)`,
                type: 'expiring'
            });
        }
    });

    // Quick-match / generic recipes if inventory exists
    if (inventory.length > 0) {
        const names = inventory.slice(0,6).map(i => i.item_name);
        if (names.length > 0) {
            suggestions.push({
                title: `Quick Stir-fry (${names.slice(0,3).join(', ')})`,
                reason: 'Based on your available ingredients',
                type: 'match'
            });
            suggestions.push({
                title: `Soup with ${names[0]}`,
                reason: 'Generic recipe',
                type: 'generic'
            });
        }
    }

    // Show "No items" only if there are no suggestions at all
    if (suggestions.length === 0) {
        suggestions.push({
            title:'No items in inventory',
            reason:'Add inventory to get suggestions',
            type:'none'
        });
    }

    // Render suggestion list
    suggestionsList.innerHTML = '';
    suggestions.forEach((s, idx) => {
        const item = document.createElement('div');
        item.className = 'suggestion-item';
        item.innerHTML = `
            <div>
                <div style="font-weight:700;color:#fff">${escapeHtml(s.title)}</div>
                <div style="font-size:0.85rem;color:#e8eefc">${escapeHtml(s.reason)}</div>
            </div>
            <div>
                <button class="btn-secondary btn-use" data-index="${idx}">Use</button>
            </div>
        `;
        suggestionsList.appendChild(item);
    });

    // Assign click handlers
    document.querySelectorAll('.btn-use').forEach(b => b.addEventListener('click', (e) => {
      const idx = e.currentTarget.dataset.index;
      const chosen = suggestions[idx];
      if (!chosen || chosen.type === 'none') {
          return alert('No suggestion available.');
      }

      // Find the first empty slot
      let found = false;
      for (let d of days) {
          for (let m of mealTypes) {
              if (!plans[d][m]) {
                  activeEditing = {day: d, mealType: m};
                  modalTitle.innerText = `Add / Edit: ${d} — ${m}`;
                  modalMealName.value = chosen.title;
                  modalRecipeSection.style.display = 'block';
                  modalRecipeSelect.innerHTML = '<option value="">— select suggestion —</option>';
                  suggestions.forEach(s => {
                      const opt = document.createElement('option');
                      opt.value = s.title;
                      opt.textContent = s.title;
                      modalRecipeSelect.appendChild(opt);
                  });
                  modalSaveMeal.style.display = 'block';
                  showModal();
                  found = true;
                  break;
              }
          }
          if (found) break;
      }

      if (!found) {
          alert('No empty meal slot available to place this suggestion.');
      }
  }));
}

function openMealModal(day, mealType) {
    activeEditing = {day, mealType};
    modalTitle.innerText = `Add / Edit: ${day} — ${mealType}`;
    modalMealName.value = plans[day][mealType]?plans[day][mealType].custom_meal_name:'';
    modalRecipeSection.style.display = 'block';

    // Populate the dropdown with **inventory items**, not suggested meals
    modalRecipeSelect.innerHTML = '<option value="">— select ingredient —</option>';
    inventory.forEach(it => {
        const opt = document.createElement('option');
        opt.value = it.item_name; // ingredient name
        opt.textContent = `${it.item_name} (${it.category}) - Qty: ${it.quantity}`;
        modalRecipeSelect.appendChild(opt);
    });

    modalSaveMeal.style.display = 'block';
    showModal();
}


function showModal(){ mealModal.classList.remove('hidden'); }
function hideModal(){ mealModal.classList.add('hidden'); activeEditing=null; }

modalRecipeSelect.addEventListener('change',()=>{ 
    if(modalRecipeSelect.value) modalMealName.value = modalRecipeSelect.value; 
});

modalSaveMeal.addEventListener('click',()=>{
    if(!activeEditing) return alert("Please select a meal slot or suggestion.");
    const name = modalMealName.value.trim();
    if(!name) return alert('Please enter a meal name.');

    if(activeEditing.day && activeEditing.mealType){
        plans[activeEditing.day][activeEditing.mealType] = {custom_meal_name:name};
    } else if(activeEditing.prefill){
        alert('Click a day/meal slot to place this suggested meal.');
        return;
    }

    hideModal();
    updatePlannerUI();
});

modalCancel.addEventListener('click', () => {
    hideModal();
});


// Assign suggestion to meal slot
// Assign suggestion to meal slot
plannerContainer.addEventListener('click', (e) => {
    if (!activeEditing || !activeEditing.prefill) return;
    const slot = e.target.closest('.meal-slot');
    if (!slot) return;
    const parts = slot.id.split("-");
    if (parts.length < 3) return;
    const day = parts[1], meal = parts[2];

    // Assign the suggested meal
    plans[day][meal] = { custom_meal_name: activeEditing.prefill };

    // Remove this suggestion from the list
    suggestions = suggestions.filter(s => s.title !== activeEditing.prefill);

    hideModal();
    updatePlannerUI();
    renderSuggestions(); // Re-render suggestion panel
});


// Save weekly plan
savePlanBtn.addEventListener('click', ()=>{
    const week_start = weekInput.value;
    const payload = [];
    days.forEach(d=>mealTypes.forEach(m=>{
        if(plans[d][m]) payload.push({day:d, meal_type:m, custom_meal_name:plans[d][m].custom_meal_name});
    }));
    fetch('api_meal_planner.php?action=save_plan',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({week_start, plans:payload})
    }).then(r=>r.json()).then(res=>{
        if(res.ok) alert('Weekly plan saved ✔️');
        else alert('Error saving plan');
    }).catch(err=>{console.error(err); alert('Network error');});
});

weekInput.addEventListener('change', fetchInventoryAndSuggestions);

function escapeHtml(s){return (s+'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}

initEmptyPlans();
buildPlannerUI();
fetchInventoryAndSuggestions();
})();
