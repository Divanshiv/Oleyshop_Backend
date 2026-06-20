# Matrix API Reference

## Authentication

All endpoints except `GET /api/v1/matrix/levels` require a Bearer token:

```
Authorization: Bearer {token}
```

---

## `GET /api/v1/matrix/status`

Get current position, level, team stats, and next level requirements.

**Response (Rahul — Silver):**

```json
{
    "user_id": 1,
    "name": "Rahul Sharma",
    "is_member": true,
    "current_level": 2,
    "current_position": "Silver",
    "total_team_members": 5,
    "direct_referrals_filled": 5,
    "direct_referrals_active": 5,
    "incentives_eligible": true,
    "direct_referrals": [
        {"id": 2, "position": 1, "is_member": true, "name": "Priya Patel", "matrix_level": 1, "matrix_position": "Bronze"},
        {"id": 3, "position": 2, "is_member": true, "name": "Amit Verma", "matrix_level": 1, "matrix_position": "Bronze"},
        {"id": 4, "position": 3, "is_member": true, "name": "Sneha Reddy", "matrix_level": 1, "matrix_position": "Bronze"},
        {"id": 5, "position": 4, "is_member": true, "name": "Vikram Singh", "matrix_level": 1, "matrix_position": "Bronze"},
        {"id": 6, "position": 5, "is_member": true, "name": "Neha Gupta", "matrix_level": 0, "matrix_position": null}
    ],
    "current_level_info": {
        "level": 2,
        "position_name": "Silver",
        "required_members": 16,
        "incentive_amount": 2500
    },
    "next_level": {
        "level": 3,
        "position_name": "Gold",
        "incentive_amount": 11000,
        "remaining_members": 64,
        "condition": "Need 4+ directs at Level 2 (Silver+)",
        "directs_ready": 0,
        "directs_required": 4
    }
}
```

**Response (Neha — No level):**

```json
{
    "user_id": 6,
    "name": "Neha Gupta",
    "is_member": true,
    "current_level": 0,
    "current_position": null,
    "total_team_members": 0,
    "direct_referrals_filled": 0,
    "direct_referrals_active": 0,
    "incentives_eligible": false,
    "direct_referrals": [],
    "current_level_info": null,
    "next_level": {
        "level": 1,
        "position_name": "Bronze",
        "incentive_amount": 1100,
        "remaining_members": 4,
        "condition": "Need 4+ active direct referrals",
        "directs_ready": 0,
        "directs_required": 4
    }
}
```

---

## `GET /api/v1/matrix/levels`

List all level definitions. No auth required.

**Response:**

```json
[
    {"level": 1, "position_name": "Bronze", "required_members": 4, "incentive_amount": 1100},
    {"level": 2, "position_name": "Silver", "required_members": 16, "incentive_amount": 2500},
    {"level": 3, "position_name": "Gold", "required_members": 64, "incentive_amount": 11000},
    {"level": 4, "position_name": "Pearl", "required_members": 256, "incentive_amount": 25000},
    {"level": 5, "position_name": "Ruby", "required_members": 1024, "incentive_amount": 51000},
    {"level": 6, "position_name": "Star", "required_members": 4096, "incentive_amount": 100000},
    {"level": 7, "position_name": "Emerald", "required_members": 16384, "incentive_amount": 251000},
    {"level": 8, "position_name": "Platin", "required_members": 65536, "incentive_amount": 500000},
    {"level": 9, "position_name": "Venus", "required_members": 262144, "incentive_amount": 1100000},
    {"level": 10, "position_name": "Ambassador", "required_members": 1048576, "incentive_amount": 2500000},
    {"level": 11, "position_name": "Diamond", "required_members": 4194304, "incentive_amount": 5100000},
    {"level": 12, "position_name": "King", "required_members": 16777216, "incentive_amount": 12100000}
]
```

---

## `GET /api/v1/matrix/tree?depth=3`

Recursive tree of referrals up to specified depth (max 5).

**Response (Rahul):**

```json
{
    "id": 1,
    "name": "Rahul Sharma",
    "phone": "+91-9876543210",
    "is_member": true,
    "position": null,
    "depth": 0,
    "matrix_level": 2,
    "matrix_position": "Silver",
    "children": [
        {
            "id": 2,
            "name": "Priya Patel",
            "phone": "+91-9876543211",
            "is_member": true,
            "position": 1,
            "depth": 1,
            "matrix_level": 1,
            "matrix_position": "Bronze",
            "children": [
                {
                    "id": 7,
                    "name": "...",
                    "phone": "...",
                    "is_member": true,
                    "depth": 2,
                    "matrix_level": 1,
                    "matrix_position": "Bronze",
                    "children": []
                }
            ]
        }
    ]
}
```

---

## `GET /api/v1/matrix/team`

Flat list of all direct team members.

**Response:**

```json
{
    "total": 5,
    "members": [
        {"id": 2, "name": "Priya Patel", "position": 1, "level": 1, "position_name": "Bronze"},
        {"id": 3, "name": "Amit Verma", "position": 2, "level": 1, "position_name": "Bronze"},
        {"id": 4, "name": "Sneha Reddy", "position": 3, "level": 1, "position_name": "Bronze"},
        {"id": 5, "name": "Vikram Singh", "position": 4, "level": 1, "position_name": "Bronze"},
        {"id": 6, "name": "Neha Gupta", "position": 5, "level": 0, "position_name": null}
    ]
}
```

---

## `GET /api/v1/matrix/incentive-history`

Awarded incentives with amounts and dates.

**Response (Rahul):**

```json
{
    "total_incentive": 3600,
    "history": [
        {"level": 1, "position_name": "Bronze", "amount": 1100, "total_team_members": 5, "status": "credited", "credited_at": "2026-06-19T19:19:38.000000Z"},
        {"level": 2, "position_name": "Silver", "amount": 2500, "total_team_members": 5, "status": "credited", "credited_at": "2026-06-19T19:50:34.000000Z"}
    ]
}
```

---

## `GET /api/v1/member-status`

Points, milestone progress, and wallet balance.

**Response:**

```json
{
    "is_member": true,
    "total_point_value": 3100,
    "next_milestone": 6500,
    "progress_percent": 100,
    "remaining_points": 0,
    "wallet_balance": 2500
}
```

---

## Level Progression Summary

| Level | Position | Required Members | Incentive | Recursive Condition |
|:-----:|:--------:|:----------------:|:---------:|---------------------|
| 1 | Bronze | 4 | ₹1,100 | 4+ active direct referrals |
| 2 | Silver | 16 | ₹2,500 | 4+ directs at Bronze+ |
| 3 | Gold | 64 | ₹11,000 | 4+ directs at Silver+ |
| 4 | Pearl | 256 | ₹25,000 | 4+ directs at Gold+ |
| 5 | Ruby | 1024 | ₹51,000 | 4+ directs at Pearl+ |
| 6 | Star | 4096 | ₹1,00,000 | 4+ directs at Ruby+ |
| 7 | Emerald | 16384 | ₹2,51,000 | 4+ directs at Star+ |
| 8 | Platin | 65536 | ₹5,00,000 | 4+ directs at Emerald+ |
| 9 | Venus | 262144 | ₹11,00,000 | 4+ directs at Platin+ |
| 10 | Ambassador | 1048576 | ₹25,00,000 | 4+ directs at Venus+ |
| 11 | Diamond | 4194304 | ₹51,00,000 | 4+ directs at Ambassador+ |
| 12 | King | 16777216 | ₹1,21,00,000 | 4+ directs at Diamond+ |
