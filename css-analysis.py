#!/usr/bin/env python3
"""
CSS Usage Analysis Script for JETXCEL Project
Analyzes CSS classes defined in header.php and checks their usage across PHP views
"""

import re
import os
from collections import defaultdict

def extract_css_classes_from_header():
    """Extract all CSS class definitions from header.php"""
    header_path = "/opt/lampp/htdocs/jetxcel2/src/includes/partials/header.php"
    css_classes = set()
    
    try:
        with open(header_path, 'r', encoding='utf-8') as f:
            content = f.read()
            
        # Find CSS class definitions (look for .classname patterns)
        css_pattern = r'\.([a-zA-Z][a-zA-Z0-9_-]*)\s*{'
        matches = re.findall(css_pattern, content)
        
        for match in matches:
            css_classes.add(match)
            
        # Also look for pseudo-classes and states
        pseudo_pattern = r'\.([a-zA-Z][a-zA-Z0-9_-]*):([a-zA-Z-]+)'
        pseudo_matches = re.findall(pseudo_pattern, content)
        for match in pseudo_matches:
            css_classes.add(match[0])  # Add base class
            
    except FileNotFoundError:
        print(f"Header file not found: {header_path}")
        
    return css_classes

def extract_used_classes_from_views():
    """Extract all CSS classes used in PHP view files"""
    views_path = "/opt/lampp/htdocs/jetxcel2/src/views"
    used_classes = defaultdict(list)
    
    try:
        for filename in os.listdir(views_path):
            if filename.endswith('.php'):
                filepath = os.path.join(views_path, filename)
                with open(filepath, 'r', encoding='utf-8') as f:
                    content = f.read()
                    
                # Find class attributes
                class_pattern = r'class=["\']([^"\']+)["\']'
                matches = re.findall(class_pattern, content)
                
                for match in matches:
                    # Split multiple classes
                    classes = match.split()
                    for cls in classes:
                        if cls and not cls.startswith('bi-'):  # Exclude Bootstrap icons
                            used_classes[cls].append(filename)
                            
    except FileNotFoundError:
        print(f"Views directory not found: {views_path}")
        
    return used_classes

def analyze_css_usage():
    """Main analysis function"""
    print("🔍 Analyzing CSS Usage in JETXCEL Project")
    print("=" * 50)
    
    # Extract defined CSS classes
    defined_classes = extract_css_classes_from_header()
    print(f"📊 Total CSS classes defined in header.php: {len(defined_classes)}")
    
    # Extract used classes
    used_classes = extract_used_classes_from_views()
    print(f"📊 Total unique classes used in views: {len(used_classes)}")
    
    # Find unused classes
    unused_classes = defined_classes - set(used_classes.keys())
    
    print("\n🚫 UNUSED CSS CLASSES:")
    print("-" * 30)
    if unused_classes:
        for cls in sorted(unused_classes):
            print(f"  .{cls}")
    else:
        print("  ✅ All defined classes are being used!")
    
    print(f"\n📈 USAGE SUMMARY:")
    print("-" * 20)
    print(f"  Defined: {len(defined_classes)}")
    print(f"  Used: {len(used_classes)}")
    print(f"  Unused: {len(unused_classes)}")
    print(f"  Usage rate: {((len(defined_classes) - len(unused_classes)) / len(defined_classes) * 100):.1f}%")
    
    # Show most used classes
    print(f"\n🔥 MOST USED CLASSES:")
    print("-" * 20)
    usage_count = {cls: len(files) for cls, files in used_classes.items()}
    top_classes = sorted(usage_count.items(), key=lambda x: x[1], reverse=True)[:10]
    
    for cls, count in top_classes:
        if cls in defined_classes:
            print(f"  .{cls}: {count} files")
    
    # Classes used but not defined (likely Bootstrap or external)
    external_classes = set(used_classes.keys()) - defined_classes
    print(f"\n🌐 EXTERNAL/BOOTSTRAP CLASSES USED: {len(external_classes)}")
    
    return {
        'defined': defined_classes,
        'used': used_classes,
        'unused': unused_classes,
        'external': external_classes
    }

if __name__ == "__main__":
    results = analyze_css_usage()
