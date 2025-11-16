<?php

/**
 * Expand FTC Safeguards Rule Controls
 *
 * Adds comprehensive controls for the FTC Safeguards Rule (16 CFR Part 314)
 * Particularly relevant for:
 * - Accounting firms (tax returns, financial statements, payroll data)
 * - Car dealerships (credit applications, financing, customer data)
 * - Other financial institutions handling customer financial information
 */

return function($db) {
    $controls = [
        // ===== GOVERNANCE AND OVERSIGHT (§314.3-314.4(a)) =====
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.3(a)', 'title' => 'Board Approval and Oversight', 'description' => 'Report to board of directors or governing body at least annually on the overall status of the information security program and compliance with the Safeguards Rule.', 'ml_level' => null, 'category' => 'Governance'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.3(b)', 'title' => 'Board Report Content', 'description' => 'Board reports must include: information security program status, material matters related to program, compliance status, and material changes to program.', 'ml_level' => null, 'category' => 'Governance'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(a)', 'title' => 'Designate Qualified Individual', 'description' => 'Designate a qualified individual responsible for implementing and supervising the information security program. Individual must report to board or governing body.', 'ml_level' => null, 'category' => 'Governance'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(a)(1)', 'title' => 'Qualified Individual Expertise', 'description' => 'Ensure the qualified individual has adequate expertise, authority, and resources to implement, supervise, and maintain the information security program.', 'ml_level' => null, 'category' => 'Governance'],

        // ===== RISK ASSESSMENT (§314.4(b)) =====
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(b)', 'title' => 'Written Risk Assessment', 'description' => 'Conduct periodic written risk assessments to identify reasonably foreseeable internal and external risks to security, confidentiality, and integrity of customer information.', 'ml_level' => null, 'category' => 'Risk Assessment'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(b)(1)', 'title' => 'Criteria for Information Systems Evaluation', 'description' => 'Identify and assess risks to customer information in each relevant area of operations, including: employee training, information systems, detecting and responding to attacks/intrusions/failures, managing system vulnerabilities, and third-party service providers.', 'ml_level' => null, 'category' => 'Risk Assessment'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(b)(2)', 'title' => 'Risk Prioritization', 'description' => 'Design information security program to control identified risks through selection of appropriate risk management strategy (accept, mitigate, or transfer). Prioritize risks based on likelihood and potential damage.', 'ml_level' => null, 'category' => 'Risk Assessment'],

        // ===== SAFEGUARDS DESIGN AND IMPLEMENTATION (§314.4(c)) =====
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(1)', 'title' => 'Access Controls', 'description' => 'Implement access controls to limit physical and electronic access to customer information to authorized individuals only. Controls must include least privilege and role-based access.', 'ml_level' => null, 'category' => 'Access Control'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(1)(i)', 'title' => 'User Access Management', 'description' => 'Establish procedures for granting, modifying, and revoking access to customer information based on job responsibilities and principle of least privilege.', 'ml_level' => null, 'category' => 'Access Control'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(1)(ii)', 'title' => 'Physical Access Controls', 'description' => 'Restrict physical access to areas where customer information is stored (file rooms, server rooms, workstations) through locks, badges, security guards, or other mechanisms.', 'ml_level' => null, 'category' => 'Access Control'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(1)(iii)', 'title' => 'Access Control Reviews', 'description' => 'Periodically review access rights and remove access for terminated employees, contractors, and those who no longer require access to customer information.', 'ml_level' => null, 'category' => 'Access Control'],

        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(2)', 'title' => 'Encryption of Customer Information', 'description' => 'Encrypt customer information in transit over external networks and at rest. Use industry-standard encryption methods (e.g., TLS 1.2+, AES-256).', 'ml_level' => null, 'category' => 'Cryptography'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(2)(i)', 'title' => 'Data in Transit Encryption', 'description' => 'Encrypt all customer information transmitted over public or untrusted networks using TLS 1.2 or higher, or equivalent encryption standards.', 'ml_level' => null, 'category' => 'Cryptography'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(2)(ii)', 'title' => 'Data at Rest Encryption', 'description' => 'Encrypt customer information stored on laptops, portable devices, removable media, and in databases using AES-256 or equivalent encryption.', 'ml_level' => null, 'category' => 'Cryptography'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(2)(iii)', 'title' => 'Encryption Key Management', 'description' => 'Implement secure encryption key management practices including key generation, storage, rotation, and destruction procedures.', 'ml_level' => null, 'category' => 'Cryptography'],

        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(3)', 'title' => 'Secure Development Practices', 'description' => 'Develop, implement, and maintain secure development practices for in-house developed applications that access, store, or transmit customer information.', 'ml_level' => null, 'category' => 'System Development'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(3)(i)', 'title' => 'Secure Coding Standards', 'description' => 'Establish and follow secure coding standards based on industry best practices (e.g., OWASP Top 10, SANS/CWE Top 25).', 'ml_level' => null, 'category' => 'System Development'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(3)(ii)', 'title' => 'Code Review and Testing', 'description' => 'Conduct security-focused code reviews and testing (static analysis, dynamic analysis, penetration testing) before deploying applications to production.', 'ml_level' => null, 'category' => 'System Development'],

        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(4)', 'title' => 'Multi-Factor Authentication', 'description' => 'Implement multi-factor authentication for any individual accessing customer information on systems. MFA must use at least two authentication factors from different categories (knowledge/possession/inherence).', 'ml_level' => null, 'category' => 'Authentication'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(4)(i)', 'title' => 'MFA for Remote Access', 'description' => 'Require MFA for all remote access to systems containing customer information, including VPN, remote desktop, and cloud-based applications.', 'ml_level' => null, 'category' => 'Authentication'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(4)(ii)', 'title' => 'MFA for Privileged Accounts', 'description' => 'Require MFA for all privileged/administrative accounts with elevated access to systems containing customer information.', 'ml_level' => null, 'category' => 'Authentication'],

        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(5)', 'title' => 'Asset Inventory', 'description' => 'Develop, maintain, and update periodically an inventory of systems, applications, infrastructure, and facilities authorized to store, process, or transmit customer information.', 'ml_level' => null, 'category' => 'Asset Management'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(5)(i)', 'title' => 'Hardware Asset Inventory', 'description' => 'Maintain inventory of all physical devices (servers, workstations, laptops, mobile devices, network equipment) that access customer information.', 'ml_level' => null, 'category' => 'Asset Management'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(5)(ii)', 'title' => 'Software Asset Inventory', 'description' => 'Maintain inventory of all software applications and databases that store, process, or transmit customer information, including version numbers and patch levels.', 'ml_level' => null, 'category' => 'Asset Management'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(5)(iii)', 'title' => 'Data Classification', 'description' => 'Classify customer information based on sensitivity and criticality to determine appropriate protection measures.', 'ml_level' => null, 'category' => 'Asset Management'],

        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(6)', 'title' => 'Change Management', 'description' => 'Implement procedures to evaluate and approve information systems changes that may materially affect security of customer information.', 'ml_level' => null, 'category' => 'Change Management'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(6)(i)', 'title' => 'Change Review and Approval', 'description' => 'Require security review and approval for all changes to systems handling customer information, including infrastructure, applications, and configurations.', 'ml_level' => null, 'category' => 'Change Management'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(6)(ii)', 'title' => 'Change Documentation', 'description' => 'Document all changes including description, business justification, security impact assessment, testing results, and approver.', 'ml_level' => null, 'category' => 'Change Management'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(6)(iii)', 'title' => 'Emergency Change Procedures', 'description' => 'Establish expedited change procedures for emergency situations while maintaining security review and post-implementation documentation.', 'ml_level' => null, 'category' => 'Change Management'],

        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(7)', 'title' => 'Log Retention and Monitoring', 'description' => 'Maintain audit logs and monitor access to customer information. Logs must be protected from tampering and retained for reasonable period.', 'ml_level' => null, 'category' => 'Monitoring'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(7)(i)', 'title' => 'Audit Log Generation', 'description' => 'Generate audit logs for all access to customer information including user ID, date/time, type of access, and files/records accessed.', 'ml_level' => null, 'category' => 'Monitoring'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(7)(ii)', 'title' => 'Log Review and Analysis', 'description' => 'Regularly review audit logs for suspicious activity, unauthorized access attempts, and policy violations. Investigate anomalies promptly.', 'ml_level' => null, 'category' => 'Monitoring'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(7)(iii)', 'title' => 'Log Protection and Retention', 'description' => 'Protect logs from unauthorized modification or deletion. Retain logs for minimum period (e.g., 90 days for authentication logs, 1 year for access logs).', 'ml_level' => null, 'category' => 'Monitoring'],

        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(8)', 'title' => 'Secure Disposal', 'description' => 'Implement procedures for secure disposal of customer information when no longer needed. Methods must prevent unauthorized access to or use of information.', 'ml_level' => null, 'category' => 'Data Protection'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(8)(i)', 'title' => 'Physical Media Destruction', 'description' => 'Destroy physical media (paper documents, hard drives, backup tapes) containing customer information using shredding, pulverizing, or degaussing.', 'ml_level' => null, 'category' => 'Data Protection'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(8)(ii)', 'title' => 'Electronic Data Sanitization', 'description' => 'Sanitize electronic media before disposal or reuse using secure deletion methods (e.g., DoD 5220.22-M, NIST 800-88 purge/destroy).', 'ml_level' => null, 'category' => 'Data Protection'],

        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(9)', 'title' => 'Response to System Failures', 'description' => 'Implement procedures to respond to system failures, errors, or other circumstances that result in exposure of customer information.', 'ml_level' => null, 'category' => 'Incident Response'],

        // ===== TESTING AND MONITORING (§314.4(d)) =====
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(d)', 'title' => 'Penetration Testing and Vulnerability Assessment', 'description' => 'Conduct periodic penetration testing and vulnerability assessments at least annually, or when infrastructure or applications undergo significant changes.', 'ml_level' => null, 'category' => 'Testing'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(d)(1)', 'title' => 'Annual Penetration Testing', 'description' => 'Conduct penetration testing at least annually by qualified personnel to identify vulnerabilities that could allow unauthorized access to customer information.', 'ml_level' => null, 'category' => 'Testing'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(d)(2)', 'title' => 'Vulnerability Scanning', 'description' => 'Perform vulnerability scans at least quarterly on all systems that access, store, or transmit customer information. Remediate critical/high vulnerabilities promptly.', 'ml_level' => null, 'category' => 'Testing'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(d)(3)', 'title' => 'Continuous Monitoring', 'description' => 'Implement continuous monitoring and security event detection for systems handling customer information using SIEM, IDS/IPS, or similar technologies.', 'ml_level' => null, 'category' => 'Testing'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(d)(4)', 'title' => 'Remediation Tracking', 'description' => 'Track and document remediation of identified vulnerabilities. Establish SLAs for remediation based on risk severity.', 'ml_level' => null, 'category' => 'Testing'],

        // ===== SECURITY AWARENESS TRAINING (§314.4(e)) =====
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(e)', 'title' => 'Security Awareness Training', 'description' => 'Train personnel to implement information security program. Training must be provided to all employees who handle customer information.', 'ml_level' => null, 'category' => 'Training'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(e)(1)', 'title' => 'Initial Security Training', 'description' => 'Provide security awareness training to all new employees before granting access to customer information. Training must cover policies, procedures, and best practices.', 'ml_level' => null, 'category' => 'Training'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(e)(2)', 'title' => 'Annual Security Training', 'description' => 'Conduct annual security awareness training for all personnel covering current threats (phishing, ransomware, social engineering) and organizational policies.', 'ml_level' => null, 'category' => 'Training'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(e)(3)', 'title' => 'Role-Based Training', 'description' => 'Provide specialized training for employees with elevated access (IT staff, developers, administrators) covering their specific security responsibilities.', 'ml_level' => null, 'category' => 'Training'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(e)(4)', 'title' => 'Training Documentation', 'description' => 'Maintain records of security training including attendee names, dates, topics covered, and acknowledgment of understanding.', 'ml_level' => null, 'category' => 'Training'],

        // ===== SERVICE PROVIDER OVERSIGHT (§314.4(f)) =====
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(f)', 'title' => 'Service Provider Oversight', 'description' => 'Take reasonable steps to select and retain service providers capable of maintaining appropriate safeguards for customer information. Require service providers by contract to implement and maintain such safeguards.', 'ml_level' => null, 'category' => 'Third Party'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(f)(1)', 'title' => 'Vendor Due Diligence', 'description' => 'Conduct due diligence before engaging service providers who will access customer information. Review security policies, certifications (SOC 2, ISO 27001), and incident history.', 'ml_level' => null, 'category' => 'Third Party'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(f)(2)', 'title' => 'Contractual Requirements', 'description' => 'Include contractual provisions requiring service providers to: implement appropriate safeguards, protect confidentiality/security of customer information, notify of breaches, and permit audits.', 'ml_level' => null, 'category' => 'Third Party'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(f)(3)', 'title' => 'Ongoing Vendor Monitoring', 'description' => 'Periodically assess service provider compliance with security requirements through reviews of SOC 2 reports, security questionnaires, or audits.', 'ml_level' => null, 'category' => 'Third Party'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(f)(4)', 'title' => 'Vendor Inventory', 'description' => 'Maintain inventory of all service providers with access to customer information including type of access, data accessed, and security controls in place.', 'ml_level' => null, 'category' => 'Third Party'],

        // ===== INCIDENT RESPONSE (§314.4(g)) =====
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(g)', 'title' => 'Incident Response Plan', 'description' => 'Implement written incident response plan to promptly respond to security events materially affecting security of customer information.', 'ml_level' => null, 'category' => 'Incident Response'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(g)(1)', 'title' => 'Incident Response Team', 'description' => 'Designate incident response team with clear roles and responsibilities. Include representatives from IT, legal, compliance, and business units.', 'ml_level' => null, 'category' => 'Incident Response'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(g)(2)', 'title' => 'Incident Classification', 'description' => 'Establish criteria for classifying security incidents by severity (critical/high/medium/low) based on type of information affected, number of customers, and potential harm.', 'ml_level' => null, 'category' => 'Incident Response'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(g)(3)', 'title' => 'Incident Response Procedures', 'description' => 'Document procedures for: detecting incidents, containment, eradication, recovery, evidence preservation, notification, and post-incident review.', 'ml_level' => null, 'category' => 'Incident Response'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(g)(4)', 'title' => 'Breach Notification', 'description' => 'Establish procedures for notifying affected customers, regulators (FTC, state AGs), and law enforcement of security breaches as required by applicable laws.', 'ml_level' => null, 'category' => 'Incident Response'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(g)(5)', 'title' => 'Incident Response Testing', 'description' => 'Test incident response plan at least annually through tabletop exercises or simulations to ensure team readiness and identify gaps.', 'ml_level' => null, 'category' => 'Incident Response'],

        // ===== MONITORING AND TESTING (§314.4(h)) =====
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(h)', 'title' => 'Program Monitoring and Testing', 'description' => 'Regularly test key controls, systems, and procedures of information security program. Frequency based on risk.', 'ml_level' => null, 'category' => 'Testing'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(h)(1)', 'title' => 'Control Effectiveness Testing', 'description' => 'Periodically test effectiveness of key security controls (access controls, encryption, MFA, logging) to ensure operating as designed.', 'ml_level' => null, 'category' => 'Testing'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(h)(2)', 'title' => 'Security Metrics', 'description' => 'Establish and track security metrics/KPIs to measure program effectiveness (e.g., time to patch, training completion rate, incidents detected).', 'ml_level' => null, 'category' => 'Testing'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(h)(3)', 'title' => 'Annual Program Review', 'description' => 'Conduct comprehensive annual review of information security program to assess adequacy and effectiveness. Update program based on findings.', 'ml_level' => null, 'category' => 'Testing'],

        // ===== SPECIFIC CONTROLS FOR ACCOUNTING FIRMS =====
        ['framework' => 'FTC-SAFEGUARDS', 'code' => 'ACCT-1', 'title' => 'Tax Return Data Protection', 'description' => 'Implement enhanced protections for tax return data including Social Security numbers, income information, and bank account details.', 'ml_level' => null, 'category' => 'Industry-Specific'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => 'ACCT-2', 'title' => 'Client Portal Security', 'description' => 'Secure client portals for document exchange with encryption, MFA, session timeouts, and audit logging of all document access/downloads.', 'ml_level' => null, 'category' => 'Industry-Specific'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => 'ACCT-3', 'title' => 'Email Encryption for Sensitive Data', 'description' => 'Prohibit sending unencrypted emails containing customer financial information. Implement secure email solutions (e.g., TLS, S/MIME, portal links).', 'ml_level' => null, 'category' => 'Industry-Specific'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => 'ACCT-4', 'title' => 'Workpaper and File Protection', 'description' => 'Protect audit workpapers, financial statements, and client files with encryption and access controls. Implement retention and disposal policies.', 'ml_level' => null, 'category' => 'Industry-Specific'],

        // ===== SPECIFIC CONTROLS FOR CAR DEALERSHIPS =====
        ['framework' => 'FTC-SAFEGUARDS', 'code' => 'AUTO-1', 'title' => 'Credit Application Protection', 'description' => 'Secure storage and transmission of credit applications containing SSN, income, employment, and credit history. Encrypt both paper and electronic forms.', 'ml_level' => null, 'category' => 'Industry-Specific'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => 'AUTO-2', 'title' => 'Test Drive Document Security', 'description' => "Protect copies of driver's licenses collected during test drives. Implement retention limits and secure disposal after transaction completion.", 'ml_level' => null, 'category' => 'Industry-Specific'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => 'AUTO-3', 'title' => 'Dealer Management System (DMS) Security', 'description' => 'Secure DMS systems storing customer financial and personal information with access controls, encryption, regular patching, and audit logging.', 'ml_level' => null, 'category' => 'Industry-Specific'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => 'AUTO-4', 'title' => 'Financing Document Retention', 'description' => 'Establish secure retention and disposal procedures for financing documents, credit reports, and loan applications. Limit retention to business need.', 'ml_level' => null, 'category' => 'Industry-Specific'],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => 'AUTO-5', 'title' => 'Trade-In Vehicle Information', 'description' => 'Protect customer information from trade-in vehicles (registration, loan payoff information). Securely erase data from vehicle systems before resale.', 'ml_level' => null, 'category' => 'Industry-Specific'],
    ];

    $inserted = 0;
    $updated = 0;

    foreach ($controls as $control) {
        // Check if control already exists
        $existing = $db->fetchOne(
            'SELECT id FROM controls WHERE framework = ? AND code = ?',
            [$control['framework'], $control['code']]
        );

        if (!$existing) {
            $db->insert('controls', $control);
            $inserted++;
        } else {
            // Update existing control with enhanced description and category
            $db->update('controls', [
                'title' => $control['title'],
                'description' => $control['description'],
                'category' => $control['category'] ?? null,
            ], 'id = :id', [':id' => $existing['id']]);
            $updated++;
        }
    }

    // Return summary
    return [
        'inserted' => $inserted,
        'updated' => $updated,
        'total' => count($controls)
    ];
};
