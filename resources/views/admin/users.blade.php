@extends('admin.layout')
@section('title', 'Users Management')

@section('content')

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <span style="font-size:14px;font-weight:700;">All Users</span>
        <div style="display:flex;gap:8px;">
            <input type="text" id="searchInput" class="form-control" placeholder="Search name / phone..." style="width:220px;" oninput="searchUsers()">
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th><th>Name</th><th>Phone</th><th>VIP</th>
                    <th>Main Bal</th><th>Winning Bal</th><th>Referral</th>
                    <th>Joined</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody id="usersTable">
                <tr><td colspan="10" style="text-align:center;color:var(--muted);padding:24px;">Loading...</td></tr>
            </tbody>
        </table>
    </div>
    <div id="pagination" style="display:flex;justify-content:center;gap:8px;margin-top:16px;"></div>
</div>

<!-- WALLET UPDATE MODAL -->
<div class="modal-overlay" id="walletModal">
    <div class="modal">
        <div class="modal-title">
            💰 Update Wallet - <span id="modalUserName"></span>
            <button class="modal-close" onclick="closeModal('walletModal')">✕</button>
        </div>
        <div class="form-group">
            <label class="form-label">Wallet Type</label>
            <select id="walletType" class="form-control">
                <option value="main">Main Wallet</option>
                <option value="winning">Winning Wallet</option>
                <option value="bonus">Bonus Wallet</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Action</label>
            <select id="walletAction" class="form-control">
                <option value="credit">Credit (Add)</option>
                <option value="debit">Debit (Deduct)</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Amount</label>
            <input type="number" id="walletAmount" class="form-control" placeholder="Enter amount" min="1">
        </div>
        <div class="form-group">
            <label class="form-label">Note (Optional)</label>
            <input type="text" id="walletNote" class="form-control" placeholder="Reason for update">
        </div>
        <div style="display:flex;gap:8px;margin-top:4px;">
            <button class="btn btn-primary" style="flex:1;" onclick="submitWalletUpdate()">Update Wallet</button>
            <button class="btn btn-outline" onclick="closeModal('walletModal')">Cancel</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentPage = 1;
let searchQuery = '';
let selectedUserId = null;

async function loadUsers(page = 1) {
    currentPage = page;
    const url = `/users?page=${page}${searchQuery ? '&search=' + searchQuery : ''}`;
    const data = await AAPI(url);
    const users = data.data || [];

    document.getElementById('usersTable').innerHTML = users.length
        ? users.map(u => `<tr>
            <td style="color:var(--muted);font-size:12px;">${u.id}</td>
            <td>
                <div style="font-weight:600;">${u.name}</div>
                <div style="font-size:11px;color:var(--muted);">${u.email || ''}</div>
            </td>
            <td style="font-family:monospace;">${u.phone}</td>
            <td><span class="badge badge-gold">VIP ${u.vip_level || 0}</span></td>
            <td style="font-weight:600;">₹${parseFloat(u.wallet?.main_balance || 0).toFixed(2)}</td>
            <td style="color:var(--green);font-weight:600;">₹${parseFloat(u.wallet?.winning_balance || 0).toFixed(2)}</td>
            <td style="font-family:monospace;font-size:12px;color:var(--primary);">${u.referral_code || '--'}</td>
            <td style="font-size:11px;color:var(--muted);">${new Date(u.created_at).toLocaleDateString('en-IN')}</td>
            <td>
                <span class="badge ${u.is_blocked ? 'badge-red' : 'badge-green'}">
                    ${u.is_blocked ? 'Blocked' : 'Active'}
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-outline" onclick="openWalletModal(${u.id}, '${u.name}')" title="Update Wallet">💰</button>
                <button class="btn btn-sm ${u.is_blocked ? 'btn-green' : 'btn-red'}" onclick="toggleBlock(${u.id})" style="margin-left:4px;" title="${u.is_blocked ? 'Unblock' : 'Block'}">
                    ${u.is_blocked ? '🔓' : '🔒'}
                </button>
            </td>
        </tr>`).join('')
        : '<tr><td colspan="10" style="text-align:center;color:var(--muted);padding:24px;">No users found</td></tr>';

    // Pagination
    if (data.last_page > 1) {
        let html = '';
        for (let i = 1; i <= data.last_page; i++) {
            html += `<button class="btn ${i === data.current_page ? 'btn-primary' : 'btn-outline'}" style="padding:6px 12px;" onclick="loadUsers(${i})">${i}</button>`;
        }
        document.getElementById('pagination').innerHTML = html;
    }
}

let searchTimer;
function searchUsers() {
    clearTimeout(searchTimer);
    searchQuery = document.getElementById('searchInput').value;
    searchTimer = setTimeout(() => loadUsers(1), 400);
}

async function toggleBlock(userId) {
    const data = await AAPI(`/users/${userId}/toggle-block`, { method: 'POST', body: JSON.stringify({}) });
    showToast(data.is_blocked ? 'User blocked' : 'User unblocked', data.is_blocked ? 'error' : 'success');
    loadUsers(currentPage);
}

function openWalletModal(userId, name) {
    selectedUserId = userId;
    document.getElementById('modalUserName').textContent = name;
    document.getElementById('walletAmount').value = '';
    document.getElementById('walletNote').value = '';
    document.getElementById('walletModal').classList.add('open');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}

async function submitWalletUpdate() {
    const amount = document.getElementById('walletAmount').value;
    if (!amount || amount <= 0) { showToast('Enter valid amount', 'error'); return; }

    const data = await AAPI(`/users/${selectedUserId}/wallet`, {
        method: 'POST',
        body: JSON.stringify({
            amount:      parseFloat(amount),
            type:        document.getElementById('walletAction').value,
            wallet_type: document.getElementById('walletType').value,
            note:        document.getElementById('walletNote').value,
        })
    });

    if (data.message) {
        showToast(data.message, 'success');
        closeModal('walletModal');
        loadUsers(currentPage);
    } else {
        showToast(data.message || 'Failed', 'error');
    }
}

loadUsers();
</script>
@endpush
