<?php

/**
 * Seed Risk Assessment Questions
 * Comprehensive cybersecurity risk assessment questionnaire
 */

return function($db) {
    $questions = [
        // ===== ACCESS CONTROL & IDENTITY MANAGEMENT =====
        [
            'category' => 'Access Control',
            'subcategory' => 'User Authentication',
            'question_text' => 'Does your organization enforce multi-factor authentication (MFA) for all users accessing sensitive systems?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'MFA significantly reduces the risk of unauthorized access. Consider implementing MFA for email, VPN, and administrative access at minimum.',
            'order_number' => 1
        ],
        [
            'category' => 'Access Control',
            'subcategory' => 'User Authentication',
            'question_text' => 'Are password policies enforced requiring minimum length (12+ characters), complexity, and regular changes?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Strong passwords are the first line of defense. NIST recommends at least 12 characters and avoiding common patterns.',
            'order_number' => 2
        ],
        [
            'category' => 'Access Control',
            'subcategory' => 'Access Management',
            'question_text' => 'Is the principle of least privilege applied to user accounts (users only have access to what they need)?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'Limiting access reduces the potential damage from compromised accounts or insider threats.',
            'order_number' => 3
        ],
        [
            'category' => 'Access Control',
            'subcategory' => 'Access Management',
            'question_text' => 'Are user access rights reviewed and recertified at least quarterly?',
            'question_type' => 'yes_no',
            'weight' => 3,
            'guidance' => 'Regular access reviews help identify and remove unnecessary permissions, especially after role changes.',
            'order_number' => 4
        ],
        [
            'category' => 'Access Control',
            'subcategory' => 'Access Management',
            'question_text' => 'Are accounts for terminated employees disabled immediately upon separation?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'Delayed account termination is a major security risk. Automate this process when possible.',
            'order_number' => 5
        ],
        [
            'category' => 'Access Control',
            'subcategory' => 'Privileged Access',
            'question_text' => 'Are privileged/administrative accounts monitored and audited separately from regular user accounts?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'Admin accounts have elevated privileges and should have enhanced monitoring and logging.',
            'order_number' => 6
        ],
        [
            'category' => 'Access Control',
            'subcategory' => 'Session Management',
            'question_text' => 'Do systems automatically lock or log out users after a period of inactivity?',
            'question_type' => 'yes_no',
            'weight' => 3,
            'guidance' => 'Automatic session timeouts prevent unauthorized access from unattended workstations. Recommended: 15 minutes for sensitive systems.',
            'order_number' => 7
        ],

        // ===== NETWORK SECURITY =====
        [
            'category' => 'Network Security',
            'subcategory' => 'Perimeter Defense',
            'question_text' => 'Is your network protected by a properly configured firewall?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'Firewalls are essential for controlling inbound and outbound network traffic. Ensure rules follow least privilege.',
            'order_number' => 10
        ],
        [
            'category' => 'Network Security',
            'subcategory' => 'Network Segmentation',
            'question_text' => 'Is your network segmented to separate sensitive systems from general user networks?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Network segmentation limits lateral movement if an attacker gains access. Use VLANs or separate subnets.',
            'order_number' => 11
        ],
        [
            'category' => 'Network Security',
            'subcategory' => 'Remote Access',
            'question_text' => 'Is all remote access to your network secured through VPN or similar encrypted tunnels?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'Unencrypted remote access exposes credentials and data. Always require VPN with MFA.',
            'order_number' => 12
        ],
        [
            'category' => 'Network Security',
            'subcategory' => 'Wireless Security',
            'question_text' => 'Are wireless networks secured with WPA3 or WPA2 encryption (minimum)?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'WPA3 is recommended. WPA2 is acceptable as minimum. Never use WEP or open networks for business use.',
            'order_number' => 13
        ],
        [
            'category' => 'Network Security',
            'subcategory' => 'Wireless Security',
            'question_text' => 'Is guest WiFi separated from the corporate network?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Guest networks should be isolated to prevent unauthorized access to corporate resources.',
            'order_number' => 14
        ],
        [
            'category' => 'Network Security',
            'subcategory' => 'Intrusion Detection',
            'question_text' => 'Do you have intrusion detection/prevention systems (IDS/IPS) monitoring network traffic?',
            'question_type' => 'yes_no',
            'weight' => 3,
            'guidance' => 'IDS/IPS can detect and block malicious activity in real-time. Consider managed security services if in-house expertise is limited.',
            'order_number' => 15
        ],

        // ===== DATA PROTECTION & ENCRYPTION =====
        [
            'category' => 'Data Protection',
            'subcategory' => 'Data Encryption',
            'question_text' => 'Is sensitive data encrypted at rest (on servers, databases, and storage devices)?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'Encryption protects data if storage media is stolen or improperly disposed of. Use AES-256 or equivalent.',
            'order_number' => 20
        ],
        [
            'category' => 'Data Protection',
            'subcategory' => 'Data Encryption',
            'question_text' => 'Is sensitive data encrypted in transit (during transmission over networks)?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'Use TLS 1.2+ for all web traffic and secure protocols (SFTP, SSH) for file transfers. Never send sensitive data unencrypted.',
            'order_number' => 21
        ],
        [
            'category' => 'Data Protection',
            'subcategory' => 'Data Classification',
            'question_text' => 'Has your organization classified data based on sensitivity (e.g., public, internal, confidential, restricted)?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Data classification helps determine appropriate security controls and handling procedures.',
            'order_number' => 22
        ],
        [
            'category' => 'Data Protection',
            'subcategory' => 'Data Backup',
            'question_text' => 'Are regular backups performed for critical systems and data?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'Follow the 3-2-1 rule: 3 copies, 2 different media types, 1 off-site. Test restoration regularly.',
            'order_number' => 23
        ],
        [
            'category' => 'Data Protection',
            'subcategory' => 'Data Backup',
            'question_text' => 'Are backup restoration procedures tested at least annually?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Untested backups may fail when needed most. Regular testing ensures you can recover from data loss events.',
            'order_number' => 24
        ],
        [
            'category' => 'Data Protection',
            'subcategory' => 'Data Retention',
            'question_text' => 'Is there a documented data retention and disposal policy?',
            'question_type' => 'yes_no',
            'weight' => 3,
            'guidance' => 'Retaining data longer than necessary increases risk and storage costs. Define retention periods based on legal/business requirements.',
            'order_number' => 25
        ],
        [
            'category' => 'Data Protection',
            'subcategory' => 'Media Sanitization',
            'question_text' => 'Is media (hard drives, USB drives, etc.) securely wiped or physically destroyed before disposal?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Simple deletion is not secure. Use certified data destruction methods (NIST 800-88 guidelines).',
            'order_number' => 26
        ],

        // ===== ENDPOINT SECURITY =====
        [
            'category' => 'Endpoint Security',
            'subcategory' => 'Malware Protection',
            'question_text' => 'Is antivirus/anti-malware software installed and updated on all endpoints (workstations, servers)?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'Endpoint protection is essential. Ensure automatic updates and real-time scanning are enabled.',
            'order_number' => 30
        ],
        [
            'category' => 'Endpoint Security',
            'subcategory' => 'Patch Management',
            'question_text' => 'Are operating systems and applications patched within 30 days of security updates being released?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'Unpatched systems are primary attack vectors. Critical patches should be applied within days, not weeks.',
            'order_number' => 31
        ],
        [
            'category' => 'Endpoint Security',
            'subcategory' => 'Device Management',
            'question_text' => 'Are all mobile devices (laptops, tablets, phones) used for work protected with encryption and remote wipe capabilities?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Mobile devices are easily lost or stolen. Implement MDM (Mobile Device Management) solutions.',
            'order_number' => 32
        ],
        [
            'category' => 'Endpoint Security',
            'subcategory' => 'Device Management',
            'question_text' => 'Are personal devices (BYOD) prohibited from accessing corporate systems, or are they managed through a formal BYOD policy?',
            'question_type' => 'yes_no',
            'weight' => 3,
            'guidance' => 'BYOD introduces security risks. If allowed, implement containerization and security requirements.',
            'order_number' => 33
        ],
        [
            'category' => 'Endpoint Security',
            'subcategory' => 'USB/Removable Media',
            'question_text' => 'Are USB drives and removable media restricted or controlled?',
            'question_type' => 'yes_no',
            'weight' => 3,
            'guidance' => 'Removable media can introduce malware or be used for data exfiltration. Consider disabling USB ports or requiring encrypted USB drives.',
            'order_number' => 34
        ],

        // ===== INCIDENT RESPONSE & BUSINESS CONTINUITY =====
        [
            'category' => 'Incident Response',
            'subcategory' => 'Incident Response Plan',
            'question_text' => 'Does your organization have a documented incident response plan?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'An IR plan defines roles, procedures, and communication strategies for responding to security incidents.',
            'order_number' => 40
        ],
        [
            'category' => 'Incident Response',
            'subcategory' => 'Incident Response Plan',
            'question_text' => 'Is the incident response plan tested at least annually through tabletop exercises or simulations?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Testing identifies gaps in your response capabilities. Practice makes perfect in a crisis.',
            'order_number' => 41
        ],
        [
            'category' => 'Incident Response',
            'subcategory' => 'Incident Detection',
            'question_text' => 'Are security logs monitored for suspicious activity?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Early detection is critical. Implement SIEM or log analysis tools for centralized monitoring.',
            'order_number' => 42
        ],
        [
            'category' => 'Incident Response',
            'subcategory' => 'Incident Reporting',
            'question_text' => 'Do employees know how to report suspected security incidents?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Clear reporting procedures ensure incidents are escalated quickly. Provide a 24/7 contact method.',
            'order_number' => 43
        ],
        [
            'category' => 'Business Continuity',
            'subcategory' => 'Disaster Recovery',
            'question_text' => 'Does your organization have a documented business continuity/disaster recovery plan?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'BC/DR plans ensure critical operations can continue during disruptions. Define RTOs and RPOs.',
            'order_number' => 44
        ],
        [
            'category' => 'Business Continuity',
            'subcategory' => 'Disaster Recovery',
            'question_text' => 'Is the business continuity plan tested at least annually?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Untested plans often fail in real disasters. Conduct full or partial failover tests regularly.',
            'order_number' => 45
        ],

        // ===== SECURITY AWARENESS & TRAINING =====
        [
            'category' => 'Security Awareness',
            'subcategory' => 'Training Programs',
            'question_text' => 'Do all employees receive security awareness training at least annually?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'Human error is a leading cause of breaches. Regular training reduces risky behavior.',
            'order_number' => 50
        ],
        [
            'category' => 'Security Awareness',
            'subcategory' => 'Phishing Training',
            'question_text' => 'Are employees trained to recognize and report phishing attempts?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'Phishing is the #1 attack vector. Consider running simulated phishing campaigns to test awareness.',
            'order_number' => 51
        ],
        [
            'category' => 'Security Awareness',
            'subcategory' => 'Phishing Training',
            'question_text' => 'Does your organization conduct simulated phishing campaigns to test employee awareness?',
            'question_type' => 'yes_no',
            'weight' => 3,
            'guidance' => 'Simulations provide measurable metrics and identify users who need additional training.',
            'order_number' => 52
        ],
        [
            'category' => 'Security Awareness',
            'subcategory' => 'Role-Based Training',
            'question_text' => 'Do employees with privileged access receive additional security training specific to their roles?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Admins, developers, and executives need specialized training due to their elevated access and responsibilities.',
            'order_number' => 53
        ],
        [
            'category' => 'Security Awareness',
            'subcategory' => 'New Hire Training',
            'question_text' => 'Do new employees receive security training during onboarding?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Security training should begin on day one. Include acceptable use policies and data handling procedures.',
            'order_number' => 54
        ],

        // ===== PHYSICAL SECURITY =====
        [
            'category' => 'Physical Security',
            'subcategory' => 'Access Control',
            'question_text' => 'Are physical access controls (locks, badges, biometrics) in place for areas containing IT equipment?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Protect server rooms, network closets, and other critical areas. Maintain access logs.',
            'order_number' => 60
        ],
        [
            'category' => 'Physical Security',
            'subcategory' => 'Visitor Management',
            'question_text' => 'Are visitors required to sign in and be escorted in secure areas?',
            'question_type' => 'yes_no',
            'weight' => 3,
            'guidance' => 'Visitor controls prevent unauthorized physical access and provide audit trails.',
            'order_number' => 61
        ],
        [
            'category' => 'Physical Security',
            'subcategory' => 'Surveillance',
            'question_text' => 'Are security cameras installed to monitor critical areas?',
            'question_type' => 'yes_no',
            'weight' => 2,
            'guidance' => 'Video surveillance deters unauthorized access and provides forensic evidence.',
            'order_number' => 62
        ],
        [
            'category' => 'Physical Security',
            'subcategory' => 'Environmental Controls',
            'question_text' => 'Are environmental controls (fire suppression, HVAC, power) in place to protect IT equipment?',
            'question_type' => 'yes_no',
            'weight' => 3,
            'guidance' => 'Environmental hazards can cause significant downtime. Implement fire suppression, temperature monitoring, and UPS systems.',
            'order_number' => 63
        ],

        // ===== ASSET MANAGEMENT =====
        [
            'category' => 'Asset Management',
            'subcategory' => 'Inventory',
            'question_text' => 'Does your organization maintain an accurate inventory of all IT assets (hardware and software)?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'You cannot protect what you don\'t know about. Maintain a CMDB or asset tracking system.',
            'order_number' => 70
        ],
        [
            'category' => 'Asset Management',
            'subcategory' => 'Software Licensing',
            'question_text' => 'Are all software applications properly licensed and tracked?',
            'question_type' => 'yes_no',
            'weight' => 3,
            'guidance' => 'Unlicensed software can introduce security risks and legal liability. Conduct regular software audits.',
            'order_number' => 71
        ],
        [
            'category' => 'Asset Management',
            'subcategory' => 'Asset Disposal',
            'question_text' => 'Is there a process for securely decommissioning and disposing of IT assets?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Improper disposal can lead to data breaches. Follow documented procedures including data sanitization.',
            'order_number' => 72
        ],

        // ===== VULNERABILITY MANAGEMENT =====
        [
            'category' => 'Vulnerability Management',
            'subcategory' => 'Vulnerability Scanning',
            'question_text' => 'Are vulnerability scans performed at least quarterly on all systems?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Regular scanning identifies security weaknesses before attackers do. Monthly scanning is recommended.',
            'order_number' => 80
        ],
        [
            'category' => 'Vulnerability Management',
            'subcategory' => 'Penetration Testing',
            'question_text' => 'Is penetration testing conducted at least annually by qualified professionals?',
            'question_type' => 'yes_no',
            'weight' => 3,
            'guidance' => 'Pen testing simulates real attacks to identify exploitable vulnerabilities. Required by many compliance frameworks.',
            'order_number' => 81
        ],
        [
            'category' => 'Vulnerability Management',
            'subcategory' => 'Remediation',
            'question_text' => 'Are identified vulnerabilities remediated based on risk priority (critical vulnerabilities within 30 days)?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'Scanning without remediation provides no value. Establish SLAs based on severity ratings.',
            'order_number' => 82
        ],

        // ===== THIRD-PARTY RISK MANAGEMENT =====
        [
            'category' => 'Third-Party Risk',
            'subcategory' => 'Vendor Assessment',
            'question_text' => 'Are security assessments conducted before engaging vendors who will access your systems or data?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Third-party breaches are common. Evaluate vendor security posture through questionnaires, audits, or certifications.',
            'order_number' => 90
        ],
        [
            'category' => 'Third-Party Risk',
            'subcategory' => 'Vendor Contracts',
            'question_text' => 'Do contracts with third parties include security requirements and incident notification clauses?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Contractual obligations ensure vendors meet your security standards and notify you of breaches.',
            'order_number' => 91
        ],
        [
            'category' => 'Third-Party Risk',
            'subcategory' => 'Ongoing Monitoring',
            'question_text' => 'Are vendors\' security practices reviewed periodically (at least annually)?',
            'question_type' => 'yes_no',
            'weight' => 3,
            'guidance' => 'Security postures change over time. Re-assess critical vendors regularly.',
            'order_number' => 92
        ],

        // ===== COMPLIANCE & GOVERNANCE =====
        [
            'category' => 'Governance',
            'subcategory' => 'Policies & Procedures',
            'question_text' => 'Does your organization have documented information security policies and procedures?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'Policies set the foundation for your security program. Review and update annually.',
            'order_number' => 100
        ],
        [
            'category' => 'Governance',
            'subcategory' => 'Policies & Procedures',
            'question_text' => 'Are security policies reviewed and updated at least annually?',
            'question_type' => 'yes_no',
            'weight' => 3,
            'guidance' => 'Policies should evolve with changing threats, technologies, and business needs.',
            'order_number' => 101
        ],
        [
            'category' => 'Governance',
            'subcategory' => 'Leadership Support',
            'question_text' => 'Does senior leadership actively support and promote the information security program?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Executive buy-in is essential for funding, resources, and creating a security culture.',
            'order_number' => 102
        ],
        [
            'category' => 'Governance',
            'subcategory' => 'Security Officer',
            'question_text' => 'Has your organization designated a Chief Information Security Officer (CISO) or equivalent role?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'A dedicated security leader ensures accountability and strategic focus on security initiatives.',
            'order_number' => 103
        ],
        [
            'category' => 'Governance',
            'subcategory' => 'Audit Logging',
            'question_text' => 'Are system logs retained for at least 90 days and protected from unauthorized modification?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Logs are critical for incident investigation and compliance. Ensure adequate retention and integrity protection.',
            'order_number' => 104
        ],
        [
            'category' => 'Governance',
            'subcategory' => 'Compliance Monitoring',
            'question_text' => 'Are compliance requirements (CMMC, HIPAA, PCI-DSS, etc.) relevant to your organization identified and monitored?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'Non-compliance can result in fines, loss of contracts, or legal action. Know your obligations.',
            'order_number' => 105
        ],

        // ===== APPLICATION SECURITY =====
        [
            'category' => 'Application Security',
            'subcategory' => 'Secure Development',
            'question_text' => 'Are security requirements incorporated into the software development lifecycle (SDLC)?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Security should be built-in, not bolted-on. Implement secure coding practices and code reviews.',
            'order_number' => 110
        ],
        [
            'category' => 'Application Security',
            'subcategory' => 'Code Review',
            'question_text' => 'Are security code reviews conducted for custom applications?',
            'question_type' => 'yes_no',
            'weight' => 3,
            'guidance' => 'Code reviews identify vulnerabilities before deployment. Use both manual and automated analysis tools.',
            'order_number' => 111
        ],
        [
            'category' => 'Application Security',
            'subcategory' => 'Application Testing',
            'question_text' => 'Are web applications tested for common vulnerabilities (OWASP Top 10)?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Use DAST (Dynamic Application Security Testing) tools to identify SQL injection, XSS, and other common flaws.',
            'order_number' => 112
        ],

        // ===== CLOUD SECURITY =====
        [
            'category' => 'Cloud Security',
            'subcategory' => 'Cloud Configuration',
            'question_text' => 'If using cloud services, are cloud resources configured according to security best practices (CIS benchmarks)?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Misconfigurations are a leading cause of cloud breaches. Use automated compliance scanning tools.',
            'order_number' => 120
        ],
        [
            'category' => 'Cloud Security',
            'subcategory' => 'Cloud Access',
            'question_text' => 'Is access to cloud management consoles protected with MFA?',
            'question_type' => 'yes_no',
            'weight' => 5,
            'guidance' => 'Cloud admin accounts control critical infrastructure. Always require MFA.',
            'order_number' => 121
        ],
        [
            'category' => 'Cloud Security',
            'subcategory' => 'Cloud Monitoring',
            'question_text' => 'Are cloud activity logs (CloudTrail, Azure Activity Logs, etc.) enabled and monitored?',
            'question_type' => 'yes_no',
            'weight' => 4,
            'guidance' => 'Cloud logs provide visibility into configuration changes and API activity. Essential for incident detection.',
            'order_number' => 122
        ],
    ];

    $inserted = 0;
    foreach ($questions as $question) {
        // Check if question already exists
        $existing = $db->fetchOne(
            'SELECT id FROM risk_assessment_questions WHERE question_text = ?',
            [$question['question_text']]
        );

        if (!$existing) {
            $question['active'] = 1;
            $question['created_at'] = date('Y-m-d H:i:s');
            $db->insert('risk_assessment_questions', $question);
            $inserted++;
        }
    }

    return $inserted;
};
